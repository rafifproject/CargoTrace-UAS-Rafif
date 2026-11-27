<script>
/**
 * Oceanic Maritime Waypoint Router with Per-Shipment Lane Variation & Smooth Curves
 */
window.buildMaritimeWaypoints = function (start, end, isAir = false, shipmentId = 1) {
    const lat1 = start[0], lon1 = start[1];
    const lat2 = end[0], lon2 = end[1];

    if (isAir) {
        return [start, end];
    }

    // Deterministic per-shipment oceanic lane spread (-2.7° to +2.7°)
    const seed = ((Number(shipmentId) || 1) * 13) % 7 - 3; // -3 .. +3
    const dLat = seed * 0.85;
    const dLon = seed * 1.05;

    const pts = [start];

    // 1. South America / Brazil -> Asia / Oceania (via Cape of Good Hope & Indian Ocean)
    if (lon1 < -25 && lat1 < 10 && lon2 > 60) {
        pts.push(
            [-32.5 + dLat, -18.0 + dLon],
            [-36.0 + dLat * 0.6, 20.0 + dLon * 0.5],
            [-23.5 + dLat * 1.4, 58.0 + dLon * 1.4]
        );
        if (lat2 > 10) {
            pts.push([4.8 + dLat * 0.25, 95.5 + dLon * 0.25], [1.2, 103.8], [14.0 + dLat * 0.4, 114.0 + dLon * 0.4]);
        } else {
            pts.push([-9.0 + dLat * 0.9, 95.5 + dLon * 0.9]);
        }
    }
    // 2. Europe / Mediterranean -> Asia / Indonesia
    else if (lat1 > 28 && lon1 > -15 && lon1 < 45 && lon2 > 60) {
        if (lon1 < 0 || lat1 > 43) {
            pts.push([43.5 + dLat * 0.3, -10.5 + dLon * 0.3], [36.0, -5.6], [35.5 + dLat * 0.2, 14.5]);
        } else if (lon1 < 20) {
            pts.push([35.5 + dLat * 0.2, 14.5]);
        }
        pts.push(
            [31.2, 32.3],
            [27.5, 34.0],
            [12.6, 43.3],
            [11.5 + dLat * 0.6, 60.0 + dLon * 0.6],
            [4.5 + dLat * 0.5, 80.5 + dLon * 0.5],
            [5.5 + dLat * 0.2, 95.5]
        );
        if (lon2 > 102 && lat2 < 5) {
            pts.push([1.2, 103.8]);
        }
    }
    // 3. Asia / Indonesia -> Europe / Mediterranean
    else if (lon1 > 60 && lat2 > 28 && lon2 > -15 && lon2 < 45) {
        pts.push(
            [5.5 + dLat * 0.2, 95.5],
            [4.5 + dLat * 0.5, 80.5 + dLon * 0.5],
            [11.5 + dLat * 0.6, 60.0 + dLon * 0.6],
            [12.6, 43.3],
            [27.5, 34.0],
            [31.2, 32.3],
            [35.5 + dLat * 0.2, 14.5]
        );
        if (lon2 < 0 || lat2 > 43) {
            pts.push([36.0, -5.6], [43.5 + dLat * 0.3, -10.5 + dLon * 0.3]);
        }
    }
    // 4. South America / Brazil -> Europe
    else if (lon1 < -25 && lat1 < 10 && lat2 > 30 && lon2 > -15 && lon2 < 40) {
        pts.push([-8.0 + dLat, -31.0 + dLon], [18.0 + dLat, -24.0 + dLon], [36.5 + dLat * 0.4, -12.0 + dLon * 0.4], [44.5, -9.5]);
    }
    // 5. South America / Brazil -> North America East Coast
    else if (lon1 < -25 && lat1 < 10 && lon2 < -50 && lat2 > 18) {
        pts.push([-4.0 + dLat * 0.6, -35.0 + dLon * 0.6], [15.0 + dLat * 0.8, -60.0 + dLon * 0.8], [26.0 + dLat * 0.4, -75.0 + dLon * 0.4]);
    }

    pts.push(end);

    // Smooth sharp waypoint corners with 2 passes of Chaikin curve subdivision
    let smoothed = pts;
    if (smoothed.length > 2) {
        for (let pass = 0; pass < 2; pass++) {
            const next = [smoothed[0]];
            for (let i = 0; i < smoothed.length - 1; i++) {
                const p0 = smoothed[i];
                const p1 = smoothed[i + 1];
                next.push([0.75 * p0[0] + 0.25 * p1[0], 0.75 * p0[1] + 0.25 * p1[1]]);
                next.push([0.25 * p0[0] + 0.75 * p1[0], 0.25 * p0[1] + 0.75 * p1[1]]);
            }
            next.push(smoothed[smoothed.length - 1]);
            smoothed = next;
        }
    }

    return smoothed;
};

window.samplePolylineByRange = function (waypoints, startFrac = 0, endFrac = 1, steps = 36) {
    if (!waypoints || waypoints.length < 2) return waypoints || [];

    const segLens = [];
    let totalLen = 0;
    for (let i = 0; i < waypoints.length - 1; i++) {
        const dLat = waypoints[i + 1][0] - waypoints[i][0];
        const dLon = waypoints[i + 1][1] - waypoints[i][1];
        const len = Math.sqrt(dLat * dLat + dLon * dLon);
        segLens.push(len);
        totalLen += len;
    }
    if (totalLen === 0) return [waypoints[0], waypoints[waypoints.length - 1]];

    const pointAt = (frac) => {
        const target = Math.max(0, Math.min(1, frac)) * totalLen;
        let acc = 0;
        for (let i = 0; i < segLens.length; i++) {
            const seg = segLens[i];
            if (acc + seg >= target || i === segLens.length - 1) {
                const t = seg > 0 ? (target - acc) / seg : 0;
                return [
                    waypoints[i][0] + (waypoints[i + 1][0] - waypoints[i][0]) * t,
                    waypoints[i][1] + (waypoints[i + 1][1] - waypoints[i][1]) * t
                ];
            }
            acc += seg;
        }
        return waypoints[waypoints.length - 1];
    };

    const out = [];
    for (let i = 0; i <= steps; i++) {
        const f = startFrac + (endFrac - startFrac) * (i / steps);
        out.push(pointAt(f));
    }
    return out;
};
</script>
