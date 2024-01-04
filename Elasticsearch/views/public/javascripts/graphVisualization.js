'use strict';

//handles rendering the graph visualization and setting up nodes and links
var graphVisualization = (function () {
    var simulation = d3.forceSimulation()
    var svgID = 'connections-graph'

    var svg
    var svgWidth
    var svgHeight

    var svgTransform

    var imgUrl
    var graphNodes

    //initializes the svg and simulation
    function initSimulation() {
        svg = d3.select("#" + svgID)
        var svg_element = document.getElementById(svgID)
        /*svgWidth = svg_element.width.baseVal.value
        svgHeight = svg_element.height.baseVal.value
        svgWidth = svg_element.getBoundingClientRect().width
        svgHeight = svg_element.getBoundingClientRect().height
        svgWidth = svg_element.getBBox().width
        svgHeight = svg_element.getBBox().height*/
        //Old incorrect way of sizing graph above ^^, correct way below
        svgWidth = svg_element.clientWidth
        svgHeight = svg_element.clientHeight

        simulation
            .force("link", d3.forceLink().id(function (d) { return d.id; }))
            .force("charge", d3.forceManyBody().strength(-80))
            .force('collision', d3.forceCollide().radius(10).strength(.5))
            .force("center", d3.forceCenter(svgWidth / 2, svgHeight / 2));

        svgTransform = true;

    }

    /* Renders the graph on the SVG and sets up the nodes, links, and degrees
        @param graphData: the complete data to be rendered
        @param color: a function that takes in a String as input and produces a
            hexadecimal number color as output.
    */
    const previewFileNameMap = {};
    function renderGraphOnSVG(graphData, color, allResults) {
        if (allResults && allResults.hits && allResults.hits.hits)
            allResults.hits.hits.forEach(hit => {
                const { _id, _source: { files = [] } = {} } = hit;
                previewFileNameMap[_id] = files[0].previewfilename;
            });

        function resetSVG() {
            simulation.nodes([])
                .on("tick", null);
            simulation.force("link").links([])
            svg.html("")
            simulation.restart()
        }

        resetSVG()

        graphNodes = graphData;

        var container = svg.append('g')
        var zoom = setupZoom(container)

        svg.call(zoom)

        var linkElement = setupLinkBehavior(container, graphData, color)

        var nodeElement = setupNodeBehavior(container, graphData, color)

        var tooltip = setupTooltipBehavior(container, graphData, color)

        simulation
            .nodes(graphData.nodes)
            .on("tick", ticked);

        simulation.force("link")
            .links(graphData.links);

        //Compute and save the degrees of each vertex
        var edges = [];

        var hasDegrees = false;
        for (var i = 0; i < graphData.links.length && !hasDegrees; i++) {
            var cur = graphData.links[i];
            if (cur.source.degree || cur.target.degree) {
                hasDegrees = true;
            }
        }

        if (!hasDegrees) {
            for (var i = 0; i < graphData.links.length; i++) {
                var cur = graphData.links[i];
                cur.source.degree = (cur.source.degree || 0) + 1;
                cur.target.degree = (cur.target.degree || 0) + 1;
            }
        }

        function ticked() {
            linkElement
                .attr("x1", function (d) { return d.source.x; })
                .attr("y1", function (d) { return d.source.y; })
                .attr("x2", function (d) { return d.target.x; })
                .attr("y2", function (d) { return d.target.y; });
            //nodeElement
            //  .attr("cx", function(d) { return d.x; })
            //.attr("cy", function(d) { return d.y; });
            nodeElement.attr("transform", function (d) {
                return "translate(" + d.x + "," + d.y + ")";
            })
        }

        //save previous transform and reattach to new 
        function saveTransform() {
            if (svgTransform) {
                zoom.scaleTo(svg, .5); // set initial map scale to .5
                svgTransform = false;
            } else {
                //console.log(d3.zoomTransform(svg.node("g"))); 
                var transformSaved = d3.zoomTransform(svg.node("g")); //get previous transform element for g
                svg.select("g").attr("transform", transformSaved); //add transform element to g
            };
        }

        saveTransform();
    }

    /* 
        @param edges: the edges of the graph
        @return the result matrix
    */
    function toSparseMatrix(edges) {
        var result = {};
        for (var i = 0; i < edges.length; i++) {
            var cur = edges[i];
            if (result[cur[0]] === undefined || result[cur[1]] === undefined) {
                result[cur[0]] = [];
                result[cur[1]] = [];
            }
            result[cur[0]].push(cur[1]);
            result[cur[1]].push(cur[0]);
        }
        return result;
    }

    /* Calls the zoom function for users zooming in and out of graph
        @param svg: the svg holding the graph
        @return the transform function
    */
    function setupZoom(svg) {
        var zoom = d3.zoom()
            .on("zoom", function () {
                svg.attr("transform", d3.event.transform);
            })
        return zoom
    }


    /* Creates circles for nodes, appends text, and handles node onClick
        @param svg: the svg holding the graph
        @param graph: the graph object 
        @param color: a color object to get node colors
        @return the nodes 
    */
    function setupNodeBehavior(svg, graph, color) {
        var node = svg.append("g")
            .attr("class", "nodes")
            //.selectAll("circle")
            .selectAll("g")
            .data(graph.nodes)
            .enter().append("g")
        node.append("circle")
            .attr("r", function (d) {
                return d.group === 1 ? 4 : Math.sqrt(d.degree || 0) + 1;
            })
            .attr("fill", function (d) { return color(d.id, 'fill'); })
            .attr("stroke", function (d) { return color(d.id, 'stroke'); })
            .attr("id", function (d) { return d.id; })
            .call(d3.drag()
                .on("start", dragStarted)
                .on("drag", dragged)
                .on("end", dragEnded));

        node.append("position") //This appends text on the tag node
            .text(function (d) {
                var result;
                if (d.title) { //This appends the title of the document and all of the tags
                    result = d.title + "";
                } else if (d.id) {
                    result = d.id + "";
                } else {
                    result = "undetermined";
                }
                if (!d.tags) {
                    result = result.split(':');
                    if (result.length > 1) {
                        result = result[1];
                    } else {
                        result = result[0];
                    }
                    return result;
                }
                return;
            })
            .attr('x', -30)
            .attr('y', 15);

        node.append("rect")
            .style("fill", "#B3A369")
            .attr("stroke", "black")
            .style("opacity", 0)
            .style("pointer-events", "none");

        node.append("text") //Deals with content of the tooltip
            .text(function (d) {
                var result;
                if (d.title) { //This appends the title of the document and all of the tags
                    result = d.title + "";
                } else if (d.id) {
                    result = d.id + "";
                } else {
                    result = "undetermined";
                }
                result = result.split('>');
                if (result.length > 1) {
                    result = result[1];
                } else {
                    result = result[0];
                }
                result = result.split('<')[0].trim();
                if (d.tags) {
                    for (var i = 0; i < d.tags.length; i++) {
                        result += "; " + d.tags[i];
                    }
                } //We instead decided to only show the folder topic to clean up the tooltip
                if (d.tags) {
                    for (var i = 0; i < d.tags.length; i++) {
                        if (d.tags[i].startsWith("Folder topic")) { //Only grab the folder topic
                            result = d.tags[i];
                        }
                    }
                }
                return result;
            })
            .attr("x", 15)
            .attr("y", 15)
            .style("fill", "black")
            .style("font-size", "24px")
            .style("font-weight", "bold")
            .style("outline", "none");


        // node.append("foreignObject")
        //     .attr('height', 100)
        //     .attr('width', 500)
        //     .attr('x', 10)
        //     .attr('y', 10)
        //     .attr('style', 'red')

        // node.append("foreignObject") //Deals with content of the tooltip
        //     .attr('x', 10)
        //     .attr('y', 10)
        //     .attr("class", "hover-preview")
        //     .html(function (d) {
        //         // console.log(`/files/thumnails/${previewFileNameMap[d.id].split('.')[0]}.jpg`)
        //         var result;
        //         if (d.title) { //This appends the title of the document and all of the tags
        //             result = d.title + "";
        //         } else if (d.id) {
        //             result = d.id + "";
        //         } else {
        //             result = "undetermined";
        //         }
        //         // result = result.split('>');
        //         // if (result.length > 1) {
        //         //     result = result[1];
        //         // } else {
        //         //     result = result[0];
        //         // }
        //         // result = result.split('<')[0].trim();
        //         // if (d.tags) {
        //         //     for (var i = 0; i < d.tags.length; i++) {
        //         //         result += "; " + d.tags[i];
        //         //     }
        //         // } //We instead decided to only show the folder topic to clean up the tooltip
        //         // if (d.tags) {
        //         //     for (var i = 0; i < d.tags.length; i++) {
        //         //         if (d.tags[i].startsWith("Folder topic")) { //Only grab the folder topic
        //         //             result = d.tags[i];
        //         //         }
        //         //     }
        //         // }
                
                var resultWrapper = "<div><span>" + result + "</span>";
                if (previewFileNameMap[d.id])
                    resultWrapper += `<img src="${`/files/thumbnails/${previewFileNameMap[d.id].split('.')[0]}.jpg`}" />`
                
        //         resultWrapper += "</div>";
        //         return resultWrapper;
        //     })
            // .attr('x', 10)
            // .attr('y', 10);
        // var dom_img = document.createElement("img")
        // dom_img.src = `/files/thumnails/${previewFileNameMap[d.id].split('.')[0]}.jpg`
        // node.append(dom_img)
            

    
        //Unneeded (duplicated?) code
        
        // node.append("title") //Deals with content of the tooltip
        //     .text(function (d) {
        //         var result;

        //         if (d.tags) {
        //             for (var i = 0; i < d.tags.length; i++) {
        //                 if (d.tags[i].startsWith("Folder topic")) { //Only grab the folder topic
        //                     result = d.tags[i];
        //                 }
        //             }
        //         }
        //         return result;
        //     });

        node.on("click", function (d) {
            var g = d3.select(this);
            g.select("circle").style("fill", "#B3A369");  //Changes color of node after clicked
            if (d.group === 1) {
                window.open('./items/show/' + d.id.split("_")[1]);
            } else {
                var query = d.id;
                var prefix = '';
                if (d.id.indexOf('Box') >= 0 || d.id.indexOf('Folder') >= 0) {
                    prefix = 'tags:';
                }
                if (query.indexOf(':') >= 0) {
                    query = d.id.split(':')[1];
                }
                url = './elasticsearch?q=' + prefix +
                    '"' + query.trim() + '"';
                window.open(url);
            }
        });

        node.on("mouseover", function (d){
            var g = d3.select(this);
            g.select("circle")
                .transition()
                .duration(200)
                .attr("r", 8);

            var bbox = g.select("text").node().getBBox();
            g.select("rect")
                .attr("x", bbox.x - 5)
                .attr("y", bbox.y - 5)
                .attr("width", bbox.width + 10)
                .attr("height", bbox.height + 10)
                .style("opacity", 1)
                .style("fill", "#B3A369");
        })
        .on("mouseout", function() { 
            var g = d3.select(this);
            var originalRadius = d3.select("circle").attr("r");
            g.select("circle")
                .transition()
                .duration(200)
                .attr("r", originalRadius);
            g.select("rect")
                .style("opacity", 0);
          });

        return node
    }

    /* Creates links between nodes and styles them
        @param svg: the svg holding the graph
        @param graph: the graph object 
        @param color: a color object to get node colors
        @return the links 
    */
    function setupLinkBehavior(svg, graph, color) {
        var linkElement = svg.append("g")
            .attr("class", "links")
            .selectAll("line")
            .data(graph.links)
            .enter().append("line")
            .attr("stroke", function (d) { return color(d.target, 'fill') === '#000000' ? color(d.target.id, 'fill') : color(d.target, 'fill'); })
            .attr("stroke-width", function (d) { return Math.sqrt(d.value); });
        return linkElement
    }

    /* Creates tooltips for nodes and adds thumbnail images to them
        @param svg: the svg holding the graph
        @param graph: the graph object 
        @param color: a color object to get node colors
        @return the tooltip 
    */
    function setupTooltipBehavior(svg, graph, color) {
        var tooltip = svg.select("body").append("div")
            .attr("class", "tooltip")
            .style("opacity", 1);

        svg.selectAll("circle")
            .on("mouseover", function (d) {
                tooltip.transition()
                    .duration(200)
                    .style("opacity", 0.9);

                tooltip.html("fffffffffffffff" + "<br>")
                    .style("left", d3.event.pageX + "px")
                    .style("top", (d3.event.pageY - 28) + "px");
            })
            .on("mouseout", function (d) {
                tooltip.transition()
                    .style("opacity", 0);
            });
        return tooltip;

        /*var tooltip = d3.select('body').append('div')
            .attr('id', 'tooltip')
            .attr('style', 'position: absolute; opacity: 0;');
        var list = document.getElementsByClassName("elasticsearch-result");
        svg.selectAll('circle')
            .on('mouseover', function(d) {
                for (let item of list) {
                    var c = item.children;
                    if (c[1].getAttribute("href") == '/items/show/' + d.id.split("_")[1]) {
                        var image = c[1].getElementsByClassName('elasticsearch-result-image');
                        for (let img of image) {
                            var string = "<img src=" + img.src + ">";
                            tooltip.html(string) //this will add the image on mouseover
                                .style("left", (d3.event.pageX + 10) + "px")     
                                .style("top", (d3.event.pageY + 50) + "px")
                            tooltip.transition().duration(200).style('opacity', 1)
                            return;
                        }
                    } else {
                        tooltip.text("No preview available")
                            .style("left", (d3.event.pageX + 10) + "px")     
                            .style("top", (d3.event.pageY + 50) + "px")
                        tooltip.transition().duration(200).style('opacity', 1)
                    }
                }
            })
            .on('mouseout', function() {
                tooltip.style('opacity', 0)
            })
            .on('mousemove', function() {
                tooltip.style('left', (d3.event.pageX+10) + 'px').style('top', (d3.event.pageY+10) + 'px')
            });*/
        return tooltip
    }

    /* Handles nodes when drag action is started
        @param d: the node being dragged
    */
    function dragStarted(d) {
        if (!d3.event.active) {
            simulation.alphaTarget(0.3).restart();
        }
        d.fx = d.x;
        d.fy = d.y;
    }

    /* Handles nodes when dragged
        @param d: the node being dragged
    */
    function dragged(d) {
        d.fx = d3.event.x;
        d.fy = d3.event.y;
    }

    /* Handles nodes when drag action is ended
        @param d: the node being dragged
    */
    function dragEnded(d) {
        if (!d3.event.active) {
            simulation.alphaTarget(0);
        }
        d.fx = null;
        d.fy = null;
    }

    /* Handles highlighting the node when the title is rolled over
        @param url: the url of the title being rolled over
        @param highlighted: if the title is rolled over or not
    */
    function rolloverHighlight(url, highlighted) {
        var highlightedNode;
        for (var i = 0; i < graphNodes.nodes.length; i++) {
            if (graphNodes.nodes[i].id.split("_")[1] == url) {
                highlightedNode = graphNodes.nodes[i];
            }
        }

        var circles = svg.selectAll("circle");
        var nodeCircle;
        for (var i = 0; i < circles._groups[0].length; i++) {
            if (circles._groups[0].item(i).id == highlightedNode.id) {
                nodeCircle = circles._groups[0].item(i);
            }
        }
        if (highlighted == true) {
            nodeCircle.setAttribute('fill', '#ffd800');
            nodeCircle.setAttribute('r', '10');
        } else {
            nodeCircle.setAttribute('fill', '#000000');
            nodeCircle.setAttribute('r', '4');
        }
    }

    return {
        renderGraphOnSVG,
        initSimulation,
        rolloverHighlight
    }
}())


