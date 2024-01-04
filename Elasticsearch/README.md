# Archiviz

Archiviz is a stack of four plug-ins for Omeka that allow users to: 

1. digitize text documents (via the OCR plugin)
2. generate tags for important people, places, and things in them (via the Autotagging NLP plugin)
3. edit and manage those tags (via the Tag Management, or TM plugin)
4. visualize and navigate their collection through an interactive knowledge graph showing where these important entities intersect (via Archiviz, a modded version of Harvard's Elasticsearch plugin for Omeka). 

Users install Archiviz in a standard Omeka Classic server, with a few additional necessary dependencies. If you've set up an Omeka server, you should be perfectly capable of executing these additional steps. If you had help setting up the server, you might want to pull in your helper for this.

## Back-end Set-up

### Modifying the server

When you're ready to start, you'll need to ssh into your Omeka server using the syntax 
```
ssh [yourusername]@[serverURLorIP]
```
where you should put your corresponding information in the brackets. You'll be prompted for your password, which you should enter. Once on the system, you should type '''sudo bash''' to give yourself admin rights to the shell. You'll need these to install the dependencies. 

From here, paste the following command into the prompt: 
```
apt install apache2 mysql-server imagemagick php-mysql php-imagick unzip net-tools libapache2-mod-php php-xml php-mysql php-mbstring php-zip php-soap php-curl php-gd php-imap php-common php-dev libmcrypt-dev php-pear php-exif tesseract-ocr-eng
```
This installs a variety of pretty standard web dev and sys admin packages necessary for getting Archiviz working. 

You'll need to configure your new Apache modules: 
```
a2enmod rewrite
```
And also enable and configure your MySQL server process: 
```
systemctl enable mysql
```
and then 
```
mysql_secure_installation
``` 
You'll be prompted to create a password. Mark this down as you'll need it to initiate the Omeka MySQL user account in the next section.

Install Tesseract for OCR functionality: 
```
apt install tesseract-ocr-eng
```

And finally sPaCy for named entity recognition: 
```
pip install nltk spacy
```
and 
```
python3 -m spacy download en_core_web_sm
``` 

**Note:** The language models for tesseract and spacy should be fine to change if your documents aren't in English, but you'll need to verify the model names.

### Imagemagick and PHP Set-up

Next, we need to change a few values on the server to devote more resources to the additional packages we've installed. To set up ImageMagick, type 
```
nano /etc/ImageMagick-7/policy.xml
``` 
This will open up a simple text editor where you need to change two lines. Comment out (type a "#" before the line) 
```
<policy domain="coder" rights="read | write" pattern="PDF" />
```
and change 
```
<policy domain="resource" name="disk" value="1GiB"/>
``` 
to 
```
<policy domain="resource" name="disk" value="8GiB"/>
```
For PHP, you'll type 
```
nano /etc/php/[version]/apache2/php.ini
```
(Again, you'll need to provide the correct version number in the bracketed portion: you may need to navigate to that filepath to check the directory name.) You'll change the value of 
```
upload\max\_filesize = 2M
```
to desired size (suggested: 30M) and the value of 
```
post \_max_size = 8M
```
Finally, 
```
nano /application/config/config.ini
``` 
and uncomment and change the value of ";upload.maxFileSize" to "10M". These changes increase the size of files Omeka, PHP, and ImageMagick will allow: you may or may not need it, but these values worked for us on our 10,000 document test collection.

### Last step: Install the plug-ins
Back in the shell, 
```bash
cd /var/www/[omeka directory]/plugins
```

Again, you'll need to check and insert the name of the Omeka directory on your server: this will change as new versions are released. Type 
```bash
git clone https://github.com/BCWrit/Elasticsearch.git
```
which will download this repo to your server. You'll also need to 
```bash
git clone https://github.com/BCWrit/Autotagging.git
```
and
```bash
git clone https://github.com/BCWrit/OCR.git
```
to grab the NER and OCR component plug-ins as well.

Then type 
```
unzip [name of zipfile]
```
to unzip the plug-ins. At this point, everything should be in working order, so we'll head back over to Omeka to give Archiviz a try.

## Front-end Set-up

### Omeka Set-up

Navigate in a web browser to the url of your Omeka server and log in. First,, we'll want to make sure the plug-ins are recognized and turned on in Omeka. Go to "Plug-ins" in the top menu and click "Install" next to "OCR", "Tag Management", and "Autotagging".

Use the menus to navigate to "Settings -> General -> ImageMagick Directory". In this menu, you'll type 
```
/usr/bin/
```
into the field for "ImageMagick Directory". In "Settings -> API", check the box for "Enable API". (Figs. 1 & 2)

Fig. 1
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/1-imagemagick-path.png "ImageMagick Path")

Fig. 2
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/2-enable-API.png "Enable API")

Next, click on your name in the top menu and navigate to API keys. Give the key a name (the name is arbitrary, it just needs to be unique), and click "Generate key". You'll need this key to work the plug-ins, so copy it somewhere. (Fig. 3)

Fig. 3
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/3-API-keys.png "API Key Generation")

That's it for the front-end set-up!

## Using Archiviz

### OCR

For the purposes of this tutorial, we'll assume that you already have text documents in your Omeka instance. And while you can read them on your computer screen, the computer itself can't yet. Therefore, we need to perform Optical Character Recognition (OCR) to get the in a state the computer can read. In the Omeka Admin interface, navigate to the OCR tab on the left side of the screen (this will only be here once you've completed the server-side install). You'll see three fields, one asking for the API key you just created, and two more asking for document IDs: you'll need to supply the range of documents you want the plug-in to OCR. You can find these numbers by navigating to the "Items" tab in the left menu, sorting by "Date Added", and hovering over the link for the first and last documents added to the collection. In the bottom left corner, a link for the item should display, with a number at the end. This is the ID Omeka assigned to the document: in our case, the documents range from 56 to 10,440, so that's what we'd put into the OCR fields (Remember these as they also work for the next step). (Fig. 4) Input your key and document IDs in the OCR screen (ours is shown below as an example) and press "OCR Documents". This may take a while as your server reads and renders your documents as plain text. (Fig. 5)

Fig. 4
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/4-document-list.png "Locating Document IDs")

Fig. 5
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/5-OCR-tab.png "Starting OCR")


### Autotagging and Tag Management

You will get a success message when completed, at which point you should navigate to the "Autotagging" tab in the left menu. Input the API key and document ID range again and press "Tag Documents". This will identify the important people, places, and things in the collection: this forms the backbone for our knowledge graph. We've also provided the "Tag Management" tab where you can edit, delete, or add tags that we're identified correctly by the NER process.

Fig. 6
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/6-autotagging.png "Tag Management")

### Elasticsearch Indexing

To connect your tags to the advanced search capabilities of Elasticsearch, you'll need to index them. To do this, go to the "Elasticsearch" tab on the left, click the "Index" tab at the top, and then click "Index". This will take a little bit and you should get a "Completed" message in the log when done.

### Archiviz Knowledge Graph

At this point, we're done with set-up and ready to check out Archiviz itself! Click on the name of your collection in the top left, then on "Search Items" in the top menu. This screen allows you to query your collection in different ways. You can search for a specific term in the search bar, or you can choose the number of nodes (corresponding to documents) you want to see by using the slider and clicking "Load Graph". The first option lets you see the world of the term you search, while the latter can be used for a more general visualization, or to limit the number of nodes displayed for less powerful systems. (Fig. 7)

Fig. 7
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/7-knowledge-graph.png "Knowledge Graph Generation")

## Navigation

When the graph loads, you will see an array of black dots in the center (representing documents in the collection), and several UI elements around the edges. The graph itself is a force-directed social network graph, which basically means that the dots, or nodes, are arranged according to how associated they are in the collection and the lines, which we will render in a second, represent their connections. At the top is a search bar with a settings button where you can further refine your search. On the left are the documents included in the current search. The right shows the entities included in these documents, identified and categorized by the NER process. (Fig. 8) 

Fig. 8
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/Screenshot%20from%202022-11-01%2010-15-45.png "User Interface")

You can expand these menus to see the entities (Fig. 9) and click on them to display them in the graph. Clicking on an entity will render a colored node corresponding to the color of the entity category and labeled with the entity name (Fig. 10). 

Fig. 9
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/Screenshot%20from%202022-11-01%2010-16-28.png "Entity Menu")

Fig. 10
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/Screenshot%20from%202022-11-01%2010-16-54.png "Entity Nodes")

You can click and drag these entities, which will rearrange them in relation to all the other selected entities. You can add or subtract entities at any point. From this view, you can see documents that are connected to one or more entity (Fig. 11) and click on the documents that may contain interesting combinations. 

Fig. 11
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/Screenshot%20from%202022-11-01%2010-18-23.png "Connected Entities")

This one (Fig. 12) contains both "Martin Luther King" and "Atlanta". You can see and click all the contained entities in the top right of this window.

Fig. 12
![alt text](https://github.com/BCWrit/Archiviz/raw/main/images/Screenshot%20from%202022-11-01%2010-18-50.png "Document Click-through")
