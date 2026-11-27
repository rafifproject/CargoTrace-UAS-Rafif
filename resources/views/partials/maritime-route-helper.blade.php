<script>
/**
 * Oceanic Maritime Waypoint Router (Prevents ships from sailing over continents)
 */
window.buildMaritimeWaypoints = function (start, end, isAir = false) {
    const lat1 = start[0], lon1 = start[1];
    const lat2 = end[0], lon2 = end[1];

    if (isAir) {
        return [start, end];
    }

    const pts = [start];

    // 1. South America / Brazil -> Asia / Oceania (via Cape of Good Hope & Indian Ocean)
    if (lon1 < -25 && lat1 < 10 && lon2 > 60) {
        pts.push([-32.5, -18.0], [-35.4, 20.0], [-24.0, 58.0]);
        if (lat2 > 10) {
            // East Asia / China / Japan / Korea via Strait of Malacca
            pts.push([5.2, 95.5], [1.2, 103.8], [14.0, 114.0]);
        } else {
            // Indonesia / Australia via Sunda / Indian Ocean
            pts.push([-8.5, 96.0]);
        }
    }
    // 2. Europe / Mediterranean -> Asia / Indonesia (via Gibraltar, Suez Canal, Red Sea, Sri Lanka, Malacca)
    else if (lat1 > 28 && lon1 > -15 && lon1 < 45 && lon2 > 60) {
        if (lon1 < 0 || lat1 > 43) {
            pts.push([43.5, -10.0], [36.0, -5.6], [35.5, 14.5]);
        } else if (lon1 < 20) {
            pts.push([35.5, 14.5]);
        }
        pts.push([31.2, 32.3], [27.5, 34.0], [12.6, 43.3], [12.0, 60.0], [5.2, 80.5], [5.5, 95.5]);
        if (lon2 > 102 && lat2 < 5) {
            pts.push([1.2, 103.8]);
        }
    }
    // 3. Asia / Indonesia -> Europe / Mediterranean (reverse Suez route)
    else if (lon1 > 60 && lat2 > 28 && lon2 > -15 && lon2 < 45) {
        pts.push([5.5, 95.5], [5.2, 80.5], [12.0, 60.0], [12.6, 43.3], [27.5, 34.0], [31.2, 32.3], [35.5, 14.5]);
        if (lon2 < 0 || lat2 > 43) {
            pts.push([36.0, -5.6], [43.5, -10.0]);
        }
    }
    // 4. South America / Brazil -> Europe (via South & North Atlantic)
    else if (lon1 < -25 && lat1 < 10 && lat2 > 30 && lon2 > -15 && lon2 < 40) {
        pts.push([-8.0, -31.0], [18.0, -24.0], [36.5, -12.0], [44.5, -9.5]);
    }
    // 5. South America / Brazil -> North America East Coast
    else if (lon1 < -25 && lat1 < 10 && lon2 < -50 && lat2 > 18) {
        pts.push([-4.0, -35.0], [15.0, -60.0], [26.0, -75.0]);
    }

    pts.push(end);
    return pts;
};

window.samplePolylineByRange = function (waypoints, startFrac = 0, endFrac = 1, steps = 28) {
    if (!waypoints || waypoints.length < 2) return waypoints || [];

    // Compute cumulative segment distances
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
