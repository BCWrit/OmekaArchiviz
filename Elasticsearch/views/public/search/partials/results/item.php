<?php $result_img = record_image($record, 'thumbnail', array('class' => 'elasticsearch-result-image')); ?>
<?php if($result_img): ?>
<a href="<?php echo $record_url; ?>"><?php echo $result_img; ?></a>
<?php endif; ?>

<ul>
    <!--<li title="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>-->

    <!--<?php if(isset($hit['_source']['itemtype'])): ?>
    <li title="itemtype"><b>Item Type:</b> <?php echo $hit['_source']['itemtype']; ?></li>
<?php endif; ?>-->

    <!--<?php if(isset($hit['_source']['collection'])): ?>
    <li title="collection"><b>Collection:</b> <?php echo $hit['_source']['collection']; ?></li>
<?php endif; ?>-->

    <?php if(isset($hit['highlight']) && isset($hit['highlight']['element.text'])): ?>
    <b>Text: </b>
    <?php
    foreach($hit['highlight']['element.text'] as $highlightFragment) {
        echo $highlightFragment . ' ...';
    }
    ?>
    <?php elseif(isset($hit['_source']['elements']) && isset($hit['_source']['element'])): ?>
    <?php $elementText = $hit['_source']['element']; ?>
    <?php $elementNames = $hit['_source']['elements']; ?>
    <?php foreach($elementNames as $elementName): ?>
    <?php if(isset($elementText[$elementName['name']]) && $elementName['name'] == "text"): ?>
    <li title="element.<?php echo $elementName['name']; ?>">
        <b><?php echo $elementName['displayName']; ?>:</b>
        <?php 
                    $text = $elementText[$elementName['name']];
                    $text = str_replace("\n", " ", $text);
                    // $textLength = count($text);
                    // if ($textLength < $maxTextLength) {
                    //     $truncated = $text;
                    // } else {
                    //     $truncated = wordwrap($text, $maxTextLength);
                    //     $truncated = explode("\n", $truncated)[0]."...";
                    // }
                ?>
    </li>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if(isset($hit['_source']['tags']) && count($hit['_source']['tags']) > 0): ?>
    <?php $tagText = $hit['_source']['element'];?>
    <p style="margin: 0px 0px 0px 0px;"><b>Tags:</b></p>
    <li title="tags" class='resultTags' style="font-size: 13px;"> <?php echo implode(", ", $hit['_source']['tags']); ?>
    </li>

    <script>
    var node = document.getElementsByClassName('resultTags');
    if (node.length == 20) {
        for (var i = 0; i < node.length; i++) {
            var html = node.item(i).innerHTML.toString();
            html = "<b>Box and Folder: </b>" + html;

            var index = html.indexOf("Geopolitical");
            if (index != -1) {
                html = html.replace(/Geopolitical Entity:/g, "");
                html = html.slice(0, index - 2) + "<br><b>Geopolitical Entities: </b>" + html.slice(index, html.length);
            }

            index = html.indexOf("Organization");
            if (index != -1) {
                html = html.replace(/Organization:/g, "");
                html = html.slice(0, index - 2) + "<br><b>Organizations: </b>" + html.slice(index, html.length);
            }

            index = html.indexOf("Date");
            if (index != -1) {
                html = html.replace(/Date:/g, "");
                html = html.slice(0, index - 2) + "<br><b>Dates: </b>" + html.slice(index, html.length);
            }

            index = html.indexOf("Person");
            if (index != -1) {
                html = html.replace(/Person:/g, "");
                html = html.slice(0, index - 2) + "<br><b>People: </b>" + html.slice(index, html.length);
            }

            index = html.indexOf("Event");
            if (index != -1) {
                html = html.replace(/Event:/g, "");
                html = html.slice(0, index - 2) + "<br><b>Events: </b>" + html.slice(index, html.length);
            }

            index = html.indexOf("Law");
            if (index != -1) {
                html = html.replace(/Law:/g, "");
                html = html.slice(0, index - 2) + "<br><b>Laws: </b>" + html.slice(index, html.length);
            }

            index = html.indexOf("Folder topic");
            if (index != -1) {
                html = html.replace(/Folder topic:/g, "");
                html = html.slice(0, index - 2) + "<br><b>Folder topics: </b>" + html.slice(index, html.length);
            }
            node.item(i).innerHTML = html;
        }
    }
    </script>

    <?php endif; ?>
    <li title="created"><b>Record Created: </b>
        <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li title="updated"><b>Record Updated: </b>
        <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>