<!--<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">-->
<script>
function helpOpen() {
    document.getElementById("helpModal").style.display = "block";
}

function helpClose() {
    document.getElementById("helpModal").style.display = "none";
}
</script>

<div id="helpModal" class="w3-modal" style="z-index: 100;  padding-top: 225px;">
    <div class="w3-modal-content w3-card-4">
        <div>
            <span onclick="helpClose()" style='color:#ffffff' class="w3-button w3-display-topright">&times;</span>
            <div class="w3-row">

                <!-- Help Pane - Tutorial -->

                <div class="w3-container"
                    style="background-color: #404040; border-width: 5px; border-color: white; border-style: solid; padding-bottom: 1vw; color: white;">
                    <h2 style="color: white; border: none;">How to Use the Graph</h2>
                    <div class="w3-half" style="padding-right: 10px;">
                        <p style='margin-bottom: 0px;'>1. Search by <b>fields,</b> like:</p>
                        <ul style='font-size: 11px;'>
                            <li><code>Kennesaw</code></li>
                            <li><code>title:"Inhabited Spaces"</code></li>
                            <li><code>itemtype:"Historical Map"</code></li>
                            <li><code>tags:forts</code></li>
                            <li><code>created:[2017-10-07 TO 2017-10-14]</code></li>
                        </ul>
                        <p style='margin-bottom: 0px;'>2. Press <b>Load Graph</b></p>
                        <p style='margin-bottom: 0px;'> 3. Select topics in filter menu you want to know about <i>in
                                connection with </i> overall topic </p>
                        <p style='font-size: 11px;'>
                            <code>Ex. I want to know about Ivan Allen’s work with the NAACP, so I will search “Ivan Allen” and select “NAACP” under organization</code>
                        </p>
                    </div>
                    <div class="w3-half" style='padding-left: 10px;'>
                        <p style='margin-bottom: 0px;'>4. Drag nodes to pull them out and clearly show connections</p>
                        <p style='margin-bottom: 0px;'>5. Rollover document titles in search result and the node on the
                            graph will highlight yellow</p>
                        <p style='margin-bottom: 0px;'>6. Click on black nodes to open the document in a separate window
                        </p>
                        <p style='margin-bottom: 0px;'>7. Nodes will turn GT Old Gold after they have been visited</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>