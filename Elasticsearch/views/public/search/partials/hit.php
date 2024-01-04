<div class="elasticsearch-result" style="padding: 0px 20px;">
    <?php 
    $model_template = Inflector::underscore($hit['_source']['model']).".php";
    $record =  Elasticsearch_Utils::getRecord($hit); 
    $record_url = isset($hit['_source']['url']) ? public_url($hit['_source']['url']) : record_url($record);
    $title = !empty($hit['_source']['title']) ? $hit['_source']['title'] : __('Untitled').' '.$hit['_source']['resulttype'];
    ?>

    <h3 id='title' class="search-titles">
        <a onMouseOver='highlight(this, true)' onMouseOut='highlight(this, false)' href="<?php echo $record_url; ?>"
            title="<?php echo htmlspecialchars($title); ?>"><?php echo $title; ?></a>
    </h3>

    <script>
    var highlight = function(d, highlighted) {
        var url = d.getAttribute('href');
        url = url.substring(12, 16);
        graphVisualization.rolloverHighlight(url, highlighted);
    };
    </script>

    <?php
    try {
        echo $this->partial("search/partials/results/$model_template", array(
            'hit' => $hit,
            'record' => $record,
            'record_url' => $record_url,
            'maxTextLength' => 300
        ));
    } catch(Zend_View_Exception $e) {
        echo "<!-- missing template $model_template -->";
    }
    ?>

    <div class="elasticsearch-result-footer">
        <span style="float:right;" title="Elasticsearch Score">Score: <?php echo $hit['_score']; ?></span>
    </div>
</div>