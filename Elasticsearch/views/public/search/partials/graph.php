<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
var sliderMax;
if (sliderMax = document.getElementById("amt").value) { //check if there is a value for slider max
    sliderMax = document.getElementById("amt").value // add the value to slider max
};

$(function() {
    $("#slider-range-min").slider({
        range: "min",
        value: 3000,
        min: 1,
        max: sliderMax,
        slide: function(event, ui) {
            $("#amount").val(ui.value);
        }
    });
    $("#amount").val($("#slider-range-min").slider("value"));
});

$(function() {
    $("#slider-range-min2").slider({
        range: "min",
        value: <?php echo htmlspecialchars(array_key_exists('nodeCount', $_GET) ? $_GET['nodeCount'] : '3000', ENT_QUOTES); ?>,
        min: 1,
        max: sliderMax,
        slide: function(event, ui) {
            $("#amount2").val(ui.value);
        }
    });
    $("#amount2").val($("#slider-range-min2").slider("value"));
});
</script>
<style>
/* Center the loader */
#loader {
    position: absolute;
    left: 50%;
    top: 50%;
    z-index: 1;
    width: 100px;
    height: 100px;
    margin: -75px 0 0 -75px;
    border: 16px solid #404040;
    border-radius: 50%;
    border-top: 16px solid #B3A369;
    width: 100px;
    height: 100px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(360deg);
    }
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
<customdiv id="elasticsearch-search">
    <form id="elasticsearch-search-form" style="margin: 10px; position: relative;">
        <input type="text" placeholder="Search keyword" title="<?php echo __('Search keywords') ?>" name="q"
            style="margin: 0 0 30px 0; border: 1px solid black; width: 100%;"
            value="<?php echo htmlspecialchars(array_key_exists('q', $_GET) ? $_GET['q'] : '', ENT_QUOTES); ?>" />
        <input type="hidden" name="showGraph" value="true">

        <div style="position: absolute; right: 0; display: flex;">
            <button type='submit' id='show-graph-button'><i class="fa fa-search"></i></button>
            <button type="button" onclick="modalOpen()"><i class="fa fa-gear"></i></button>
            <button type="button" onclick="helpOpen()"><i class="fa fa-question"></i></button>
            <?php echo $this->partial('search/partials/advancedsearch.php')?>
            <?php echo $this->partial('search/partials/help.php')?>
        </div>

        <div id='onboarding' style="padding: 0px 100px;">
            <h2 style='text-align: center;'> How to Begin </h2>
            <p class="w3-half" style="padding-right:20px; margin-bottom: 0;">To search a <b>specific term</b>, use the
                search bar at the top.</p>
            <p class="w3-half" style="padding-left:20px; margin-bottom: 10px;">To visualize the <b>entire
                    collection</b>, press Load Graph at the bottom.</p>
            <p style="font-size:.9vw; margin:0 0 30px 0;">For further help on using the graph, click on the question
                mark to the right of the search bar.</p>

            <!-- Slider -->
            <div style="display: inline;">
                <h3 style="text-align: center;">Graph Density</h3>
                <div id="slider-range-min"></div>
                <label for="amount" style="padding: 0;">No. of nodes to be visualized:</label>
                <input type="text" id="amount" name="nodeCount" value="3000" readonly
                    style="border:0; font-weight:bold; float: none; width: min-content;">
                <p style="margin-top: 0px; font-size: .9vw;"> Use the slider above to limit the node count. The higher
                    the node count, the heavier the load placed on the computer. You will still see all search results
                    in the left column.</p>
                <input type='submit' value='Load Graph' id='network' style='margin-top: 30px; margin-left: 43%;'>

            </div>
        </div>
    </form>
</customdiv>
<div style='width: 100%; height: inherit; display: block;'>
    <div id="selected-tags" style='padding-top: 65px; padding-right: 280px;'></div>
    <svg display='none' id='connections-graph'
        style='width: 72%; height: 80%; padding-top: 0px; justify-content: left;'></svg>
    <div id="loader"></div>
</div>

<script>
if (document.getElementById("amount").style.display = "inline") {
    document.getElementById('amount2').disabled = true;
};

document.getElementById("loader").style.display = "none";
var showGraph = function() {
    window.location.href = graphDataRequester.setURLParam('showGraph', 'true');
};
(function() {
    var displayGraph = graphDataRequester.getURLParam('showGraph');
    if (displayGraph === 'true') {
        jQuery('#connections-graph').show();
        jQuery('#network').hide();
        document.getElementById("onboarding").style.display = "none";
        document.getElementById("loader").style.display = "block";
        document.getElementById("amount").style.display = "none";
        document.getElementById("advancedSearchSlider").style.display = "block";

        if (document.getElementById("amount").style.display = "none") {
            document.getElementById('amount').disabled = true;
            document.getElementById('amount2').disabled = false;
        };

        myFunction();
        var completeGraphData
        graphVisualization.initSimulation();
        // graphDataRequester.requestCompleteGraphData(3000)

        var maxDocumentsValue
        if (maxDocumentsValue = document.getElementById("amount2").value !==
            3000) { //checks if there is a max value
            maxDocumentsValue = document.getElementById("amount2").value // gets max amt of nodes from field
        } else {
            maxDocumentsValue = 3000 //default max
        }

        graphDataRequester.requestCompleteGraphData(maxDocumentsValue) // limits the amt of nodes loaded
            .then(function(data) {
                completeGraphData = data
                //console.log(completeGraphData);
                //graphVisualization.renderGraphOnSVG(completeGraphData, graphColors.getTagColor)
                // D3 does weird things to the nodes in complete data the first time it is run, and this
                // makes it not work with the filters, so I need to call this twice because I am a
                // potato

                var results = <?php echo json_encode($results) ?>;

                graphVisualization.renderGraphOnSVG(graphFilterer.filterGraphData([], completeGraphData),
                    graphColors.getTagColor, results)
                
                filterMenu.generateFilterMenu(
                    results['aggregations']['tags']['buckets'],
                    completeGraphData, graphColors.getTagCategoryList()
                );
            })
    }
}());

function myFunction() {
    myVar = setTimeout(showPage, 8000);
}

function showPage() {
    document.getElementById("loader").style.display = "none";
}
</script>