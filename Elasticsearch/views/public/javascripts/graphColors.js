//Defines colors of graphs and allows other files to get those colors
var graphColors = (function () {
    //Sets color-blind accessible colors for tag categories
    var tagCategoryColors = {
        "Person": {
            //fill: '#1F78B5',
            //stroke: '#0f3462'
            fill: '#CC79A7',    //Accessible pink
            stroke: '#CC79A7'
        },
        //"Facility": {
        //fill: '#B2DF8A',
        //stroke: '#617745'
        //},
        "Organization": {
            //fill: '#33A02C',
            //stroke: '#205016'
            fill: '#D55E00',    //Accessible red
            stroke: '#D55E00'
        },
        "Geopolitical Entity": {
            //fill: '#FB9A99',
            //stroke: '#765550'
            fill: '#0072B2',    //Accessible dark blue
            stroke: '#0072B2'
        },
        /*"Location": {
            fill: '#E31A1C',
            stroke: '#620A0C'
        },*/
        "Event": {
            //fill: '#FDBF6F',
            //stroke: '#776737'
            fill: '#F0E442',    //Accessible yellow
            stroke: '#F0E442'
        },
        "Law": {
            //fill: '#FF7F00',
            //stroke: '#773700'
            fill: '#009E73',    //Accessible green
            stroke: '#009E73'
        },
        "Date": {
            //fill: '#51371E',
            //stroke: '#11070E',
            fill: '#56B4E9',    //Accessible light blue
            stroke: '#56B4E9'
        },
        "Misc": {
            fill: '#CAB2D6',
            stroke: '#656173'
        },
        "Box": {
            fill: '#993300',
            stroke: '#502000'
        },
        "Folder topic": {
            //fill: '#A6CEE3',  //Accessible orange
            //stroke: '#536762'
            fill: '#E69F00',
            stroke: '#E69F00'
        },
        "Folder": {
            fill: '#A6CEE3',
            stroke: '#536762'
        },
        ".": {
            fill: '#000000',
            stroke: '#000000'
        }
    }

    /*Returns the list of possible categories
        @return the list of tag categories
    */
    var getTagCategoryList = function () {
        var tagCategoryList = [];
        for (var category in tagCategoryColors) {
            if (category !== ".") {
                tagCategoryList.push(category);
            }
        }
        return tagCategoryList;
    }

    /* Returns the tag color defined above
        @param tagText: the text of the tag
        @param colorType: the id of the color
        @param delimiter: the delimiter of the tags
        @return the color of the tag
    */
    var getTagColor = function (tagText, colorType, delimiter) {
        delimiter = delimiter || ":";

        if (typeof (tagText) !== "string") {
            tagText = ".";
        }

        var category = tagText.split(delimiter)[0].trim();
        if (tagText.startsWith("Folder topic")) {
            category = "Folder topic";
        } else if (tagText.startsWith("Box") &&
            tagText.indexOf("Folder") >= 0) {
            category = "Folder";
        } else if (tagText.startsWith("Box")) {
            category = "Box";
        }

        var colors = tagCategoryColors[category] || tagCategoryColors["."];
        return colors[colorType];
    }

    return {
        getTagColor,
        getTagCategoryList
    };
}());