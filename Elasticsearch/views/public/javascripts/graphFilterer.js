//filters the graph data based on tags or search 
var graphFilterer = (function () {

    /* Returns the nodes if they fall under the filter
        @param tagName = the node or tag passed in
        @param filters: the filter strings
        @return the nodes that fall under the filter
    */
    function passesFiltersNodes(tagName, filters) {
        return filters.some(function (filter) {
            return tagName === filter;
            /*if (tagName != undefined) {
                return tagName.toString().includes(filter);
            }*/
        });
    }

    /* Returns the links if the node falls under the filter
        @param tagName = the node or tag passed in
        @param filters: the filter strings
        @return the nodes that fall under the filter
    */
    function passesFiltersLinks(tagName, filters) {
        return filters.some(function (filter) {
            return tagName === filter;
        });
    }

    /* Grabs only the filtered data
        @param filterStrings = the strings to filter
        @param graphData: the complete graph data
        @return the filtered nodes and links
    */
    function filterTagsFromGraphData(filterStrings, graphData) {
        return {
            nodes: graphData.nodes.filter(function (node) {
                return node.group === 1 || passesFiltersNodes(node.id, filterStrings)
                /*if (node.tags != undefined) {
                    return passesFiltersNodes(node.tags[0], filterStrings);
                    //for (var i = 0; i < node.tags.length; i++) {
                      //  return passesFiltersNodes(node.tags[i], filterStrings)   
                    //}
                } */

                // return true;
            }),
            links: graphData.links.filter(function (link) {
                if (typeof link.target === "string") {
                    return filterStrings.includes(link.target);
                }
                return passesFiltersLinks(link.target.id, filterStrings)

                // return true;
            })
        }
    }

    /* Filters out rare tags if less than minimumMentionCount documents contain that tag
        @param graphData: the complete graph data
        @param minimumMentionCount: the minimum times a tag must be mentioned
        @return the filtered graphData
    */
    function filterRareTags(graphData, minimumMentionCount) {
        var tagCounts = {}
        for (var i = 0; i < graphData.links.length; i++) {
            var tagName = graphData.links[i].target
            if (tagCounts[tagName]) {
                tagCounts[tagName]++
            } else {
                tagCounts[tagName] = 1
            }
        }
        var i = 0
        while (i < graphData.links.length) {
            var tagName = graphData.links[i].target
            var tagCount = tagCounts[tagName]
            if (tagCount < minimumMentionCount) {
                graphData.links.splice(i, 1)
            } else {
                i++
            }
        }
        i = 0
        while (i < graphData.nodes.length) {
            var tagName = graphData.nodes[i].id
            var tagCount = tagCounts[tagName] || minimumMentionCount
            if (tagCount < minimumMentionCount) {
                graphData.nodes.splice(i, 1)
            } else {
                i++
            }
        }
        return graphData
    }

    /* Handles filtering the data and returning it
        @param exclusionFilterRegexStrings: the filter strings
        @param completeData: the complete graph data
        @return the filtered graphData
    */
    function filterGraphData(exclusionFilterRegexStrings, completeData) {
        var filteredData = filterRareTags(
            filterTagsFromGraphData(exclusionFilterRegexStrings, completeData),
            2)
        return filteredData
    }

    return {
        filterGraphData,
        filterRareTags,
        filterTagsFromGraphData
    }
}());
