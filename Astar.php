<script>

    // Define the start and end points
let start;
let end;

// Create a Leaflet map and add a click event listener to set the end point
const map = L.map('map').setView([14.40251, 121.46744], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 25,
    attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map); 
map.on('click', (event) => {
    end = [event.latlng.lat, event.latlng.lng];
    L.Routing.control({
                    waypoints: [
                      L.latLng(start[0], start[1]),
                      L.latLng(end[0], end[1])
                    ],
                    routeWhileDragging: true
                  }).addTo(map);
                  
    /*L.marker(end).addTo(map);*/
});
    

navigator.geolocation.getCurrentPosition((position) => {
    start = [position.coords.latitude, position.coords.longitude];
    L.marker(start).addTo(map);
});


// Define the heuristic function using the Euclidean distance between the two points
function heuristic(a, b) {
    const dx = a[0] - b[0];
    const dy = a[1] - b[1];
    return Math.sqrt(dx * dx + dy * dy);
}


// Define the A* function
function astar(start, end) {
    // Initialize the open and closed lists
    const open = [start];
    const closed = [];

    // Loop through the open list until it is empty or the goal is found
    while (open.length > 0) {
        // Find the node with the lowest cost in the open list
        let lowestIndex = 0;
        for (let i = 0; i < open.length; i++) {
            if (open[i].f < open[lowestIndex].f) {
                lowestIndex = i;
            }
        }
        const current = open[lowestIndex];

        // If the current node is the goal, return the path
        if (current[0] === end[0] && current[1] === end[1]) {
            const path = [];
            let node = current;
            while (node.previous) {
                path.push(node.previous);
                node = node.previous;
            }
            L.Routing.control({
                    waypoints: [
                      L.latLng(start[0], start[1]),
                      L.latLng(end[0], end[1])
                    ],
                    routeWhileDragging: true
                  }).addTo(map);
                  return path;
            
        }
        

        // Remove the current node from the open list and add it to the closed list
        open.splice(lowestIndex, 1);
        closed.push(current);

        // Add the current node's neighbors to the open list
        for (const neighbor of getNeighbors(current)) {
            if (!closed.includes(neighbor)) {
                // Calculate the total cost of reaching the goal through this neighbor
                const g = current.g + heuristic(current, neighbor);
                const h = heuristic(neighbor, end);
                const f = g + h;

                // If this neighbor is not in the open list, or if the new path is cheaper than the old one, update the cost and add it to the open list
                if (!open.includes(neighbor) || f < neighbor.f) {
                    neighbor.g = g;
                    neighbor.h = h;
                    neighbor.f = f;
                    neighbor.previous = current;
                    if (!open.includes(neighbor)) {
                        open.push(neighbor);
                    }
                }
                
            }
        }
    }

    // If the open list is empty and the goal was not found, return "no path found"
    return "no path found";
    
}

</script>