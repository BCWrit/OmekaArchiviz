<?php if ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>

<?php queue_css_file('elasticsearch-results'); ?>
<?php queue_css_url('https://www.w3schools.com/w3css/4/w3.css'); ?>
<?php queue_css_url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'); ?>
<?php queue_js_file('elasticsearch'); ?>
<?php queue_js_string('ElasticsearchPlugin.setupSearchResults();'); ?>

<?php queue_css_file('graphStyle'); ?>
<?php queue_css_file('filterStyle'); ?>
<?php queue_css_file('fix-w3'); ?>
<?php queue_js_url('https://d3js.org/d3.v4.min.js'); ?>
<?php queue_js_file('graphColors'); ?>
<?php queue_js_file('graphVisualization'); ?>
<?php queue_js_file('graphFilterer'); ?>
<?php queue_js_file('graphDataRequester'); ?>
<?php queue_js_file('filterMenu'); ?>
<?php echo head(array('title' => __('Elasticsearch'), 'bodyclass' => 'w3Page'));?>

<style>
.pagination {
    width: inherit;
    height: inherit;
    margin-top: 0;
    float: right;
}

.page-input {
    color: black;
}
</style>

<h1>Archiviz</h1>
<!-- SEARCH BAR -->
<div class='w3-col m5 l4' style="height: 700px;">
    <div id='elasticsearch-search_block'>
        <div id="elasticsearch-searchbar">
            <?php echo $this->partial('search/partials/searchbar.php', array('query' => $query)); ?>
        </div>
    </div>
    <?php
        if ($results):
        ?>
    <div id="elasticsearch-documents" style="overflow-y:scroll; height:70%; ">
        <?php
                if(count($results['hits']['hits']) > 0):
                    foreach($results['hits']['hits'] as $hit):
                        echo $this->partial('search/partials/hit.php', array('hit' => $hit));
                    endforeach;
                else:
                    echo __("Search did not return any results.");
                endif;
                ?>
    </div>
    <div id="elasticsearch-footer" style="border-top: 3px solid;">
        <div class="w3-col l4" id="result-amt">	
            <?php $totalResults = isset($results['hits']['total']) ? $results['hits']['total']['value'].' '.__('results') : null; ?>
            <?php echo " $totalResults" ?>
            <input id="amt" type="hidden" name="r" value=<?php echo "$totalResults"; ?>>
        </div>
        <div class='w3-col l8' style="float:right;">
            <?php echo pagination_links(); ?>
        </div>
    </div>
    <?php
        else:
        ?>
    <div>
        <h2><?php echo __("Search failed"); ?></h2>
        <p><?php echo __("The search query could not be executed. Please check your search query and try again."); ?>
        </p>
    </div>
    <?php
        endif;
        ?>
</div>

<!-- Graph and filter menu -->
<div class='w3-col m7 l8' id="graph-and-filter" style="height: 700px; position: relative;">
    <?php
        if($results):
        ?>
    <div style="position: absolute; right: 55px; padding-top: 50px;">
        <ul class="fa-ul" style="position:relative" id="tags"></ul>
        <ul class="fa-ul" style="position:relative" id="slider-text"></ul>
    </div>
    <div id="graph" style="height: inherit;">
        <?php echo $this->partial('search/partials/graph.php', array('results' => $results) ); ?>
    </div>
    <?php
        else:
        ?>
    <div>
        <h2><?php echo __("Search failed"); ?></h2>
        <p><?php echo __("The search query could not be executed. Please check your search query and try again."); ?>
        </p>
    </div>
    <?php
        endif;
        ?>
</div>

<script>
var closeTag = function() {
    var node = event.srcElement;
    node.remove();
    filterMenu.untoggleTag(node.id, node.textContent);
}

//Handles the radio buttons for filter menu sorting
var sortTags = function() {
    var radios = document.getElementsByName('sortStyle');
    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].checked) {
            // do whatever you want with the checked radio
            filterMenu.changeSortType(false);
            // only one radio can be logically checked, don't check the rest
            break;
        } else if (radios[1].checked) {
            filterMenu.changeSortType(true);
            // only one radio can be logically checked, don't check the rest
            break;
        }
    }
}
</script>

<?php echo foot(); ?>

<?php endif ?>
