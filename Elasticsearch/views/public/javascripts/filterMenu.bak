'use strict';
//Handles the creation, sorting, and rendering of filter menu on left side
var filterMenu = (function () {
    var popularSort;
    var initTags;
    var initGraph;
    var initCategories;
    var initDelimiter;
    var remade = new Boolean(false);
    var globalFilters;

    /* Filters the graph data by the tags
        @param filters: the strings to filter by
        @param graph: the graph object to filter
        @return the filter strings
    */
    var filter = function (filters, graph) {
        var filterArray = [];
        for (var category in filters) {
            var iter = filters[category].values();
            for (var i = 0; i < filters[category].size; i++) {
                filterArray.push(iter.next().value);
            }
        }

        var filteredGraph = graphFilterer.filterGraphData(filterArray, graph);
        graphVisualization.renderGraphOnSVG(filteredGraph, graphColors.getTagColor);

        return filters;
    };

    /* Toggles category filter on or off
        @param filters: the strings to filter by
        @param baseFilters: the base filters of Box, Folder, Folder Topic, Misc
        @param category: the category being toggled
        @return boolean if toggle is on or off
    */
    var toggleCategoryFilter = function (filters, baseFilters, category) {
        if (filters[category] === undefined) {
            filters[category] = new Set();
        }

        if (filters[category].size === baseFilters[category].size) {
            filters[category] = new Set();
            // filter turned off
            return false;
        } else {
            // copy the set from the base filters
            filters[category] = new Set(baseFilters[category]);
            // filter turned on
            return true;
        }
    };

    /* Toggles individual tag filter on or off
        @param filters: the strings to filter by
        @param baseFilters: the base filters of Box, Folder, Folder Topic, Misc
        @param tag: the tag being toggled
        @param category: the category being toggled
        @return boolean if toggle is on or off
    */
    var toggleTagFilter = function (filters, baseFilters, tag, category) {
        if (filters[category] === undefined) {
            filters[category] = new Set();
        }

        if (filters[category].has(tag)) {
            globalFilters = filters;
            filters[category].delete(tag);
            var node = document.getElementById("selected-tags");
            tag = cleanTag(tag);
            var index = node.innerHTML.lastIndexOf(tag);
            node.innerHTML = node.innerHTML.substring(0, index - 89 + category.length) + node.innerHTML.substring(index + tag.length + 9, node.innerHTML.length);
            // filter turned off
            return false;
        } else {
            globalFilters = filters;
            filters[category].add(tag);
            var node = document.getElementById("selected-tags");
            tag = cleanTag(tag);
            node.innerHTML = node.innerHTML + "<button class='tag-button' id='" + category + "' data-category='" + category + "' style='border: 2px solid " + graphColors.getTagColor(category, "fill") + ";' onClick='closeTag()'>" + tag + "</button>";
            // filter turned on
            return true;
        }
    };

    /* Un-toggles individual tag filter after it is removed
     * from the entity list at top of graph
        @param category: the category being toggled
        @param tag: the tag being toggled
    */
    var untoggleTag = function (category, tag) {
        var newTag = category + ":" + tag;
        tag = tag.trim();
        var node = document.getElementsByClassName('filter-button');
        var button;
        for (var i = 0; i < node.length; i++) {
            if (node[i].textContent.includes(tag)) {
                button = node[i];
            }
        }
        if (button != null) {
            selectTagFilterButton(button, category, false);
        }

        globalFilters[category].delete(newTag);
        filter(globalFilters, initGraph);
    }

    /* Cleans up tags by removing the category prefix 
     * and removes ampersands which mess with the html
        @param tag: the tag to clean
        @return cleaned tag
    */
    var cleanTag = function (tag) {
        if (tag.includes("Person:")) {
            tag = tag.replace("Person:", "");
        } else if (tag.includes("Organization:")) {
            tag = tag.replace("Organization:", "");
        } else if (tag.includes("Geopolitical Entity:")) {
            tag = tag.replace("Geopolitical Entity:", "");
        } else if (tag.includes("Date:")) {
            tag = tag.replace("Date:", "");
        } else if (tag.includes("Event:")) {
            tag = tag.replace("Event:", "");
        } else if (tag.includes("Law:")) {
            tag = tag.replace("Law:", "");
        } else if (tag.includes("Folder topic:")) {
            tag = tag.replace("Folder topic:", "");
        }
        if (tag.includes("&")) {
            tag = tag.replace("&", "and");
        }
        return tag;
    }

    /* Returns the base filters (including Folder topic, Folder, Box, and Misc)
        @param tags: the tags to filter
        @param graph: the graph object
        @param categories: the categories to filter
        @param delimiter: the delimiter of the tags
        @return base filters collection
    */
    var getBaseFilters = function (tags, graph, categories, delimiter) {
        var baseFilters = {};
        for (var category in categories) {
            baseFilters[categories[category]] = new Set();
        }
        baseFilters['Misc'] = new Set();

        for (var tag in tags) {
            var tagText = tags[tag].key;

            var category = tagText.split(delimiter)[0].trim();
            if (tagText.startsWith("Folder topic")) {
                category = "Folder topic";
            } else if (tagText.startsWith("Box") &&
                tagText.indexOf("Folder") >= 0) {
                category = "Folder";
            } else if (tagText.startsWith("Box")) {
                category = "Box";
            }

            if (baseFilters[category] === undefined) {
                category = 'Misc';
            }
            baseFilters[category].add(tagText);
        }
        return baseFilters;
    };

    /* Handles tag filter button style changes
        @param button: the tag filter button
        @param category: the category of the tag
        @param filtersTurnedOn: if the tag has been toggled
    */
    var selectTagFilterButton = function (button, category, filtersTurnedOn) {
        var fill = "transparent";
        if (filtersTurnedOn) {
            fill = graphColors.getTagColor(category, "fill");
        }
        button.style.backgroundColor = fill;
    };

    /* Handles tag filter button onClick
        @param filters: the strings to filter by
        @param baseFilters: the base filters of Box, Folder, Folder Topic, Misc
        @param tag: the tag to filter
        @param category: the category of the tag
        @param delimiter: the delimiter of the tags
        @param graph: the graph object
        @return tag button
    */
    var getTagFilterElement = function (filters, baseFilters, tag, category, delimiter, graph) {
        var button = document.createElement("button");
        button.classList.add("filter-button");
        var splitTag = tag.split(delimiter);
        splitTag = splitTag[splitTag.length - 1].split(category);
        splitTag = splitTag[splitTag.length - 1].trim();
        button.textContent = splitTag;

        button.onclick = function () {
            var filtersTurnedOn = toggleTagFilter(
                filters, baseFilters, tag, category
            );

            selectTagFilterButton(button, category, filtersTurnedOn);

            filter(filters, graph);
        };
        return button;
    }

    /* Sorts tags alphabetically
        @param ul: the unordered list of tags
    */
    var alphSort = function (ul) {
        var list, i, switching, shouldSwitch;
        if (typeof ul == "string") {
            ul = document.getElementById(ul);
        }
        switching = true;

        // Make a loop that will continue until
        // no switching has been done:
        while (switching) {
            switching = false;
            var originalList = ul.getElementsByTagName("LI");
            // Loop through all list items:
            for (i = 0; i < (originalList.length - 1); i++) {
                shouldSwitch = false;
                // Check if the next item should
                // switch place with the current item:
                if (originalList[i].innerHTML.toLowerCase() > originalList[i + 1].innerHTML.toLowerCase()) {
                    // If next item is alphabetically lower than current item,
                    // mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
            if (shouldSwitch) {
                // If a switch has been marked, make the switch
                // and mark the switch as done:
                originalList[i].parentNode.insertBefore(originalList[i + 1], originalList[i]);
                switching = true;
            }
        }
    }

    /* Sorts tags by popularity
        @param ul: the unordered list of tags
    */
    var popSort = function (ul) {
        var list, i, switching, shouldSwitch;
        if (typeof ul != "undefined") {
            ul = document.getElementById(ul);
        }
        switching = true;

        // Make a loop that will continue until
        // no switching has been done:
        while (switching) {
            switching = false;
            if (ul != null) {
                var originalList = ul.getElementsByTagName("LI");
                // Loop through all list items:
                for (i = 0; i < (originalList.length - 1); i++) {
                    shouldSwitch = false;
                    // Check if the next item should
                    // switch place with the current item:
                    if (originalList[i].size() > originalList[i + 1].size()) {
                        // If next item has less documents than current,
                        // mark as a switch and break the loop:
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                // If a switch has been marked, make the switch
                // and mark the switch as done:
                originalList[i].parentNode.insertBefore(originalList[i + 1], originalList[i]);
                switching = true;
            }
        }
    }

    /* Change if sorted popularity or alphabetically
        @param sortType: a boolean if the tags should be sorted by popularity or not
    */
    var changeSortType = function (sortType) {
        popularSort = sortType;
        remade = true;
        generateFilterMenu(initTags, initGraph, initCategories, initDelimiter);
    }

    /* Handles onclick of category filter elements
        @param filters: the strings to filter by
        @param baseFilters: the base filters of Box, Folder, Folder Topic, Misc
        @param category: the category of the tag
        @param delimiter: the delimiter of the tags
        @param graph: the graph object
        @return container of category
    */
    var getCategoryFilterElement = function (
        filters, baseFilters, category, delimiter, graph) {
        var container = document.createElement("div");
        container.classList.add("category-filter-container");
        container.style.borderColor = graphColors.getTagColor(
            category, "stroke"
        );

        var arrow = document.createElement("i");
        arrow.setAttribute("class", "arrow fa-li fa fa-chevron-right");
        container.appendChild(arrow);

        var button = document.createElement("button");
        button.classList.add("filter-button");
        button.textContent = category;
        container.appendChild(button);

        var tagFilters = document.createElement("ul");
        tagFilters.classList.add("tag-filter-list");
        tagFilters.style.display = "none";
        tagFilters.style.borderColor = container.style.borderColor;

        var iter = baseFilters[category].values();
        for (var i = 0; i < baseFilters[category].size; i++) {
            var tag = iter.next().value;
            var tagFilterElement = getTagFilterElement(
                filters, baseFilters, tag, category, delimiter, graph
            );

            var li = document.createElement("li");
            li.style.maxWidth = "150px";
            //Changed from 300, need to wrap text?
            li.appendChild(tagFilterElement);
            tagFilters.appendChild(li);
        }

        if (popularSort == true) {
            popSort(tagFilters);
        } else {
            alphSort(tagFilters);
        }
        container.appendChild(tagFilters);

        arrow.onclick = function () {
            arrow.classList.toggle("fa-chevron-right");
            arrow.classList.toggle("fa-chevron-down");
            if (tagFilters.style.display === "none") {
                tagFilters.style.display = "";
            } else {
                tagFilters.style.display = "none";
            }
        };

        button.onclick = function () {
            var filtersTurnedOn = toggleCategoryFilter(
                filters, baseFilters, category
            );

            /* 11/11/19 - changed from transparent to snow */
            // fixed problem of changing to transparent after onclick and deselecting category
            var fill = "snow";
            if (filtersTurnedOn) {
                fill = graphColors.getTagColor(category, "fill");
            }
            container.style.backgroundColor = fill;

            for (var i = 0; i < tagFilters.children.length; i++) {
                //selectTagFilterButton(
                //  tagFilters.children[i].children[0], category, false
                //);
            }

            filter(filters, graph);
        };
        return container;
    };

    /* Initializes filter menu and generates it to display
        @param tags: the tag list
        @param graph: the graph object
        @param categories: the categories list
        @param delimiter: the delimiter of the tags
    */
    var generateFilterMenu = function (tags, graph, categories, delimiter) {
        delimiter = delimiter || ":";
        initTags = tags;
        initGraph = graph;
        initCategories = categories;
        initDelimiter = delimiter;
        var baseFilters = getBaseFilters(
            tags, graph, categories, delimiter
        );

        const selectedTags = document.getElementById("selected-tags")
        var filters = {};
        if (selectedTags && selectedTags.children && selectedTags.children.length) {
            for (let i = 0; i < selectedTags.children.length; i += 1) {
                const node = selectedTags.children[i];
                category = node.getAttribute("data-category");
                if (!filters[category]) {
                    filters[category] = new Set();
                }
                filters[category].add(`${category}:${node.innerHTML}`)
            }
        }

        var categoryFilterElementList = document.getElementById("tags");
        var buttonContainer = document.createElement("div");
        buttonContainer.innerHTML = "\
        <h2 style:'padding-top: 100px;'> Visualize Connections</h2>\
        <h5>Sort tags by:</h5>\
        <fieldset id='sort-buttons'>\
            <p style='margin-top: 0;'>\
                <input type='radio' id='Alphabetical' name='sortStyle' checked = 'checked' value='Alphabetical' onclick='sortTags()'>\
                <label for='Alphabetical' style='padding: 0px 8px 0px 0px; font-weight: normal;'>Alphabetical</label>\
                \
                <input type='radio' id='Popular' name='sortStyle' value='Popular' onclick='sortTags()'>\
                <label for='Popular' style='padding: 0; font-weight: normal'>Popular</label>\
            </p>\
        </fieldset>";
        if (remade == true) {
            categoryFilterElementList.innerHTML = "";
        }

        if (popularSort == true) {
            buttonContainer.innerHTML = "<h2 style:'padding-top: 100px;'>Visualize Connections</h2>\
            <h5>Sort tags by:</h5>\
            <fieldset id='sort-buttons'>\
                <p style='margin-top: 0;'>\
                    <input type='radio' id='Alphabetical' name='sortStyle' value='Alphabetical' onclick='sortTags()'>\
                    <label for='Alphabetical' style='padding: 0px 8px 0px 0px; font-weight: normal;'>Alphabetical</label>\
                    \
                    <input type='radio' id='Popular' name='sortStyle' value='Popular' checked = 'checked' onclick='sortTags()'>\
                    <label for='Popular' style='padding: 0; font-weight: normal'>Popular</label>\
                </p>\
            </fieldset>";
        }

        categoryFilterElementList.appendChild(buttonContainer);

        var sliderExp = document.getElementById("slider-text");
        var expContainer = document.createElement("div");
        expContainer.innerHTML = "\
        <p style='font-size: .9vw; margin: 0;'> To update the number of nodes,  </p>\
        <p style='font-size: .9vw; margin: 0;'>click the gear icon to the right of</p>\
        <p style='font-size: .9vw; margin: 0;'>the search bar.</p>";
        sliderExp.innerHTML = "";
        sliderExp.appendChild(expContainer);

        for (var category in baseFilters) {
            if (baseFilters[category].size !== 0) {
                if ((!category.startsWith("Box")) && (!category.startsWith("Misc"))) {
                    if (category.startsWith("Folder")) {
                        if (!category.includes("opic")) {
                            return;
                        } else {
                            var categoryFilterElement = getCategoryFilterElement(
                                filters, baseFilters, category, delimiter, graph
                            );
                            var li = document.createElement("li");
                            li.appendChild(categoryFilterElement);
                            categoryFilterElementList.appendChild(li);
                            return;
                        }
                    } else {
                        var categoryFilterElement = getCategoryFilterElement(
                            filters, baseFilters, category, delimiter, graph
                        );
                        var li = document.createElement("li");
                        li.appendChild(categoryFilterElement);
                        categoryFilterElementList.appendChild(li);
                    }
                }
            }
        }

    }
    return {
        changeSortType,
        generateFilterMenu,
        untoggleTag
    }
}());

