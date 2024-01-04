<!--<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
<script>
function modalOpen() {
    document.getElementById("advancedSearchModal").style.display = "block";
}

function modalClose() {
    document.getElementById("advancedSearchModal").style.display = "none";
}
</script>

<div id="advancedSearchModal" class="w3-modal" style="z-index: 150; padding-top: 225px;">
    <div class="w3-modal-content w3-card-4" style='width: 800px;'>
        <div>
            <span onclick="modalClose()" style='color:#ffffff' class="w3-button w3-display-topright">&times;</span>
            <div class="w3-row">

                <!-- Help Pane - Advanced Search -->

                <div class="w3-container"
                    style="background-color: #404040; border-width: 5px; border-color: #ffffff; border-style: solid; padding-bottom: 1vw; color: white;">
                    <h2 style="color: white; border: none;">Advanced Search</h2>
                    <!--<div class="w3-container">  
                        <form id="elasticsearch-search-form">
                            <input type="text"
                                   class="w3-input"
                                   placeholder="Search by title, text, tags, etc." 
                                   title="<?php echo __('Search keywords') ?>"
                                   name="q"
                                   value="<?php echo htmlspecialchars(array_key_exists('q', $_GET) ? $_GET['q'] : '', ENT_QUOTES); ?>" />
                            <input class="w3-button" type="submit" value="Search"/>
                        </form>
                    </div>-->


                    <!-- Max Node Count Input
                    <label for="maxDocs" style="color: white;"> Max Node Count </label> <br>
                    <input type="text"
                           title="<?php echo __('Max node count on load') ?>"
                           name="n"
                           style="width: 10%;"
                           id="maxDocs"
                           value="<?php echo htmlspecialchars(array_key_exists('n', $_GET) ? $_GET['n'] : '', ENT_QUOTES); ?>" />

                    <input type='submit' value='Search' style="justify-content: center;" id='show-graph-button'/> -->


                    <!-- Max Node Count Slider -->
                    <div id="advancedSearchSlider">
                        <label for="amount2">Maximum node count:</label>
                        <input type="text" id="amount2" name="nodeCount"
                            value="<?php echo htmlspecialchars(array_key_exists('nodeCount', $_GET) ? $_GET['nodeCount'] : '', ENT_QUOTES); ?>"
                            readonly
                            style="border:0; font-weight:bold;  float: none; color: white; background-color:#404040">
                        <div id="slider-range-min2"></div>
                        <input type='submit' value='Submit' style="margin-top: 10px;" id='show-graph-button' />
                    </div>


                    <!-- Tips -->
                    <h2 style="margin-top: 5vw; color: white; border: none;">Tips</h2>
                    <div class="w3-half">
                        <p style="margin-bottom: 5px;">Search with boolean operands or wildcards:</p>
                        <ul style="font-size: 11px; margin-left: 10px;">
                            <li><code>paris AND fortifications</code></li>
                            <li><code>title:paris AND itemType:Text</code></li>
                            <li><code>featured:true</code></li>
                            <li><code>184?s OR 185?s</code></li>
                            <li><code>updated:[2017-12-01 TO *] AND resulttype:exhibit</code></li>
                        </ul>
                    </div>
                    <div class="w3-half">
                        <p style="margin: 0px;"><b>Boost</b> specific terms:</p>
                        <p style="font-size: 12px; margin-top: 0px; margin-bottom: 5px;">Use the boost operator ^ to
                            make one term more relevant than another. The default boost is 1.</p>
                        <ul style="font-size: 11px; margin-left: 10px;">
                            <li><code>paris^2 western</code></li>
                            <li><code>apple banana^5</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>