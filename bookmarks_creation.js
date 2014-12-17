M.bkmCreation = {

	chapterRootNodeClass: '.book_content',
	chapterRootNode: null,
	currentSelection: null,

	DB_PATH: M.cfg.wwwroot+'/blocks/bookmarks/dbaccess.php',

	init: function(Y, bookmark_creation_key) {
		// CACHE
		this.Y = Y;
		this.btn_backToChapter = Y.one('.btn_backToChapter');
		this.btn_storeSelection = Y.one('.btn_storeSelection');
		this.fld_bookmarkTitle = Y.one('.fld_bookmarkTitle');

		// initialize chapter container as DOM object
		var chapterRootNode = Y.one(this.chapterRootNodeClass);
		if (chapterRootNode !== null) { this.chapterRootNode = chapterRootNode.getDOMNode() }
		if(!this.chapterRootNode) alert('????? Error: Cannot determine chapter root node.');


		// assign keyboard event
		// ==================================================================
		//YUI().use("event-key", function (Y) { // referenced already in php script... //});
		//Y.Node.DOM_EVENTS.key.eventDef.KEY_MAP.space = '32'; // e.keyCode
		//document.onkeypress = M.bkmCreation.begin_bookmark_creation;
		var keyboardShortcut = 'up:'+bookmark_creation_key+'+ctrl+shift';
		Y.one(document).on('key', this.beginBookmarkCreation, keyboardShortcut, this); // Y = context 
		
		// assign bookmark creation button
		// ==================================================================
		this.btn_storeSelection.on('click', this.beginBookmarkCreation, this);

		// assign bookmark AJAX ?? store
		this.fld_bookmarkTitle.on('key', this.finishBookmarkCreation, 'up:enter', this); 


		this.fld_bookmarkTitle.on('key', this.abortBookmarkCreation, 'up:esc', this); 
		//Y.one('.fld_bookmarkTitle').on('blur', this.abortBookmarkCreation, 'up:esc', this); 

		// OBJASNI TU U KLODU KOJA JE PROCEDURA ZA BOOKMARKS??? DEKLE BOOKMARK SE NA ENTER POHRANJUJE, a ne na botun, treba li nam onda botun?? treba da spremimo selekciju (kao capture selection)







		// get DOM elements tree and selection offsets
		// focus to input elemnt to enter bookmark title (esc key to abort, stavi u upute)

		// dovoljna je smao jedna instanca!! ograniči to jer botuni su vezani samo za prvu skriptu...

		// pohrana u bazu
	},

	// 01. User selects the text and then the script temporarily stores the selection
	// It triggers by accessing bookmark creation button or assigned keyboard shortcut
	// ============================
	beginBookmarkCreation: function(e){
		// Get user selection
		// var a = M.bkmCreation._getSelectionPosition();
		this.currentSelection = this._getSelectionPosition();
		if(this.currentSelection == null) return false; // if there is no selection in the area of chapter's root element

		// Selection is stored now, focus title input field and wait
		this.fld_bookmarkTitle.focus();

		// if user doesn't enter a title, the selection should be earsed ?? NE?
	},

	// 02. after user has entered a bookmark title and pressed enter key
	// ============================
	finishBookmarkCreation: function(e){
		// kreiraj mjesto za povratak na chapter

		// selection is stored before?
		if(!this.currentSelection) return false;

		// store bookmark to database
		var title = null;
		var titleVal = this.fld_bookmarkTitle.get('value');
		if(titleVal) title = titleVal;
		var ajax = this._storeBookmarkToDatabase(title, this.currentSelection)


		// daj status
		this.btn_backToChapter.setStyle('display', 'block');
		this.btn_backToChapter.focus();

		// resetiraj sva polja...
	},

	// ============================
	_storeBookmarkToDatabase: function(title, selection){

		var dbData = {
			op: 'insert',
			start_offset: selection.startOffset,
			end_offset: selection.endOffset,
			start_nodetree: selection.startNodeTree.toString(),
			end_nodetree: selection.endNodeTree.toString(),
			title: title
		}

		Y.io(this.DB_PATH, {
			data: dbData,
			method: 'post',
			on: {
				success: function(id, o) {
					debugger;
					alert('SUCCESS')
					//M.block_accessibility.show_message(M.util.get_string('saved', 'block_accessibility'));
					//setTimeout("M.block_accessibility.show_message('')", 5000);
				},
				failure: function(id, o) {
					alert('FAILED')
					//alert(M.util.get_string('jsnosave', 'block_accessibility')+' '+o.status+' '+o.statusText);
				},
				//start: M.block_accessibility.show_loading
			}
		});
	},

	// get start and end node, their tree path and offsets of selection within those nodes
	// ============================
	_getSelectionPosition: function(){
		// initialize return value
		var selection = {
			startNode: null,
			endNode: null,
			startOffset: -1,
			endOffset: -1,
			startNodeType: undefined,
			endNodeType: undefined,
			startNodeTree: null,
			endNodeTree: null
		}

		// http://www.quirksmode.org/dom/range_intro.html
		try {
			// FOR MODERN BROWSERS
			if (window.getSelection) { 
				var userSelection = document.getSelection();
				
				// conversion: We need Range object, NOT Selection object
				var rangeObject = getRangeObject(userSelection);
				function getRangeObject(selectionObject) {
					if (selectionObject.getRangeAt)
						return selectionObject.getRangeAt(0);
					else { // Safari!
						var range = document.createRange();
						range.setStart(selectionObject.anchorNode,selectionObject.anchorOffset);
						range.setEnd(selectionObject.focusNode,selectionObject.focusOffset);
						return range;
					}
				}
				
				// populate data from Range object
				selection.startNode = rangeObject.startContainer;
				selection.endNode = rangeObject.endContainer;
				selection.startOffset = rangeObject.startOffset;
				selection.endOffset = rangeObject.endOffset;
				//rangeObject.commonAncestorContainer				
				
			}
			// FOR IE (this must come last!!)
			else if (document.selection) { 
				userSelection = document.selection.createRange();
				alert('????? ERROR: Ovo još nije implementirano')
			}	// else userSelection = document.getSelection(); // no need for this...
			
		} 
		catch (err) {
			alert(' ????? It seems your browser cannot be used for creating user bookmarks. Please use a moderm browser. Error Message: ' + err)
		}

		// check if selection is within book chapter?
		// TO-DO M.bkmCreation.chapterRootNode return null;

		// populate node types
		selection.startNodeType = null;
		selection.endNodeType = null;

		// populate node trees
		selection.startNodeTree = M.bkmCreation._calculateNodelistTree(selection.startNode);
		selection.endNodeTree = M.bkmCreation._calculateNodelistTree(selection.endNode);

		return selection;
	},

	// determine depth of parent elements (specific location path as a node tree of parents)
	// ============================
	_calculateNodelistTree: function(node){
		var rootNode = M.bkmCreation.chapterRootNode;
		var nodeTree = Array();
		var tempParent = node;
		do{
			tempChild = tempParent;
			tempParent = tempParent.parentNode;
			
			
			// determine index of child node within specific parent element in the tree
			var children = tempParent.childNodes;
			var itemIndex = -1;
			for (var ci=0, clen = children.length; ci<clen; ci++){ 
				//console.log(children.item(ci) + ' '+ (children.item(ci) === tempChild) + ' ' + ci);
				if (children.item(ci) === tempChild){ 
					itemIndex = ci;
					break;
				}
			}
			
			nodeTree.unshift(itemIndex)

		}while(tempParent !== rootNode)
		
		//console.log(JSON.stringify(nodeTree))
		return nodeTree;
	}


	


}