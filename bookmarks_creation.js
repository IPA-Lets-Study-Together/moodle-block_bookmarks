// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 
/**
 * @package    block_bookmarks
 * @copyright  Copyright 2013 onwards University of Split, Faculty of Economics 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.bkmCreation = {

 /*

	HOW IT WORKS:
	CREATING BOOKMARKS:
	1. A user selects a fragment of Moodle Book chapter text to create a bookmark from
	2. The selection has focus. The Bookmark creation process will be triggered by pressing Ctrl+Shift+Space key combination
	3. The bookmark title textbox gets visible and focused
	4. A user inputs the bookmark title into the textbox. Pressing ENTER creates bookmark (Pressing ESC or leaving the textbox field aborts bookmark creation process)
	5. Bookmark is created. Status message gets the focus (status message is "return to chapter" link). Press ENTER to jump back to the chapter

	USING BOOKMARKS:
	1. A user access desired bookmark from the bookmarks list to jump to the relevant part of the text
	2. Javascript parse given data "on-the fly" to create pins where the bookmark should appear within the chapter text
	3. Starting pin of the bookmark gets focused. A user continues to read the chapter text
	4. Ending pin is reached and focused
	5. If ending pin was accessed? Previously used item of a list of all bookmarks gets the focus. Otherwise, starting and ending pin are being cleared. A user continues to read the chapter text



*/
	chapterRootNodeClass: 'book_content',
	chapterRootNode: null,
	currentSelection: null,

	bkmStartPinClass: 'bookmark_start',
	bkmEndPinClass: 'bookmark_end',
	bkmLinkClass: 'bookmark_link',

	// unique id generation
	ID_PREFIX : 'bookmark_block_',
	USED_IDs : new Array(),
	ID_ATTR_LENGTH : 10, // generated id attribute length + ID_PREFIX part

	DB_PATH: M.cfg.wwwroot+'/blocks/bookmarks/modifybookmarks.ajax.php',

	chapterid: null,
	transactionsCount: 0, // AJAX transactions (for loader icon)

	init: function(Y, bookmark_creation_key, chapterid) {
		// CACHE
		// ==================================================================
		this.Y = Y;
		this.chapterid = chapterid;
		this.btn_backToChapter = Y.one('.btn_backToChapter');
		this.btn_insertBookmark = Y.one('.btn_insertBookmark');
		//this.btn_storeSelection = Y.one('.btn_storeSelection'); // this button is removed
		this.form_insertBookmark = Y.one('.form_insertBookmark');
		this.fld_bookmarkTitle = Y.one('.fld_bookmarkTitle');
		this.bookmarkTitleLabel = Y.one('.bookmarkTitleLabel');
		this.bookmarksList = Y.one('.bookmarks_listing ul');

		// TO-DO: Check if block is displyed within moodle book chapter (check context) - 'site-index' is also allowed for this block to be displayed
		// TO-DO: check for # hash mark within URL to focus desired bookmark directly on load
		// IMPORTANT: There should be only one block instance on the page since all the buttons are related to a single block


		// initialize chapter container as DOM object
		// ==================================================================
		var chapterRootNode = Y.one('.'+this.chapterRootNodeClass);
		if (chapterRootNode !== null) { this.chapterRootNode = chapterRootNode.getDOMNode() }
		//if(!this.chapterRootNode) alert('????? Error: Cannot determine chapter root node.');


		// assign keyboard event (to begin bookmark creation process)
		// ==================================================================
		//YUI().use("event-key", function (Y) { // referenced already in php script... //});
		//Y.Node.DOM_EVENTS.key.eventDef.KEY_MAP.space = '32'; // e.keyCode
		//document.onkeypress = M.bkmCreation.begin_bookmark_creation;
		var keyboardShortcut = 'up:'+bookmark_creation_key+'+ctrl+shift';
		Y.one(document).on('key', this.beginBookmarkCreation, keyboardShortcut, this); // Y = context 
		
		// assign bookmark creation button
		// ==================================================================
		// TO-DO: this would be alternative to capture selection before entering title. Would be nice to have it
		//this.btn_storeSelection.on('click', this.beginBookmarkCreation, this);

		// assign bookmark creation textbox and action on enter (to finish bookmark creation process)
		// ==================================================================
		//this.fld_bookmarkTitle.on('key', this.finishBookmarkCreation, 'up:enter', this); 
		this.form_insertBookmark.on('submit', this.finishBookmarkCreation, this); 
		this.fld_bookmarkTitle.on('key', this.abortBookmarkCreation, 'up:esc', this); 
		this.fld_bookmarkTitle.on('blur', this.abortBookmarkCreation, this); 
		this.btn_insertBookmark.hide(); // hide it, since textbox blur aborts the action, accessing this button will cause it as well
		this.form_insertBookmark.hide();

		// assign back to chapter link to access after bookmark is created 
		// ==================================================================
		this.btn_backToChapter.on('click', this.accessBookmarksEnd);

		// Initialize all previous bookmarks, assign onclick event
		// ==================================================================
		Y.all('.bookmarks_listing ul a').each(function(bkm){
			var randomID = M.bkmCreation._generateUniqueId(M.bkmCreation.ID_ATTR_LENGTH);
			bkm.setAttribute('href', '#'+randomID);
			bkm.setAttribute('id', 'link_'+ randomID);
			bkm.setAttribute('class', M.bkmCreation.bkmLinkClass);
			bkm.on('click', M.bkmCreation.accessBookmark);
		});
		// kao live u jQuery
		/*	YUI().use('event', function(Y) {
  				Y.delegate("click", M.bkmCreation.accessBookmark, "a", ".bookmarks_listing");
		});*/



		// Check if this browser is supported
		if (typeof window.getSelection !== "undefined") 
			Y.all('.browser-unsupported-message').hide();


	},





	// 01. User selects the text and then the script temporarily stores the selection
	// It triggers by accessing bookmark creation button or assigned keyboard shortcut
	// ============================
	beginBookmarkCreation: function(e){
		this._clearChapterTextFromPins();

		// Get user selection
		// var a = M.bkmCreation._getSelectionPosition();
		this.currentSelection = this._getSelectionPosition();
		if(this.currentSelection == null) return false; // if there is no selection in the area of chapter's root element

		// Selection is stored now, focus title input field and wait
		this.form_insertBookmark.show();
		this.fld_bookmarkTitle.focus();
		// if user doesn't enter a title, the selection should be earsed (abortBookmarkCreation will be triggerd) 
	},

	// 02. after user has entered a bookmark title and pressed enter key
	// ============================
	finishBookmarkCreation: function(e){
		// to cancel submit
		e.preventDefault();
		//Y.util.Event.preventDefault(e); 

		// selection is stored before?
		if(!this.currentSelection) return false;

		// store bookmark to database
		var title = '';
		var titleVal = this.fld_bookmarkTitle.get('value');
		if(titleVal) title = titleVal;
		var dbData = {
			op: 'insert',
			sesskey: M.cfg.sesskey,
			chapterid: this.chapterid,
			start_offset: this.currentSelection.startOffset,
			end_offset: this.currentSelection.endOffset,
			start_nodetree: this.currentSelection.startNodeTree.toString(),
			end_nodetree: this.currentSelection.endNodeTree.toString(),
			title: title
		}
		var ajax = this._storeBookmarkToDatabase(dbData);


		// delete no bookmarks status message (this chapter doesn't contain any bookmarks)
		Y.all('.no-bookmarks').remove();

		// dodaj dinamički na listu, make sure to follow how php is generating it...
		var randomID = this._generateUniqueId(this.ID_ATTR_LENGTH);
		var newLI = document.createElement("li");
		var newA = document.createElement("a");
		newA.setAttribute('data-startNodeTree', dbData.start_nodetree);
		newA.setAttribute('data-endNodeTree', dbData.end_nodetree);
		newA.setAttribute('data-startOffset', dbData.start_offset);
		newA.setAttribute('data-endOffset', dbData.end_offset);
		newA.setAttribute('href', '#'+randomID);
		newA.setAttribute('id', 'link_'+ randomID);
		newA.setAttribute('class', this.bkmLinkClass);
		if(!title) title = M.util.get_string('untitled-bkm-item', 'block_bookmarks');
		newA.innerHTML = title;
		Y.one(newA).on('click', this.accessBookmark);
		newLI.appendChild(newA);
		this.bookmarksList.append(newLI);

		// kreiraj mjesto za povratak
		// display bookmark creation status and focus it
		// TO-DO: it would be cool to make some beep sound for success and failed status
		// TO-DO: ARIA LIVE regions https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/ARIA_Live_Regions
		this.btn_backToChapter.setAttribute('data-endNodeTree', dbData.end_nodetree);
		this.btn_backToChapter.setAttribute('data-endOffset', dbData.end_offset);
		this.btn_backToChapter.setAttribute('href', '#'+randomID);
		this.btn_backToChapter.setStyle('display','block'); // show() doesn't work
		this.btn_backToChapter.on('blur', function(){ Y.one(this).hide(); })
		this.btn_backToChapter.focus();

		// on bkm title text box blur, the abortBookmarkCreation will be triggerd and form will be reset 
		return false; 
	},

	// if user selection is captured but bookmark label is not confirmed and stored to a db. Occurs if user leaves bookmark label text field without pressing enter or using ESC button
	// ============================
	abortBookmarkCreation: function(e){
		this.fld_bookmarkTitle.set('value', '');
		this.form_insertBookmark.hide();
		this.currentSelection = null;
	},

	// from a main menu jump to a bookmark located somewhere between the chapter. Dinamically create start and end points
	// ============================
	accessBookmark:function(){
		M.bkmCreation._clearChapterTextFromPins();


		var id = this.getAttribute('href').substring(1);
		var name = this.get('innerHTML');
		// TO-DO: check if pin exists already

		var startPinEl = M.bkmCreation._createBkmStartPin(id, name);
		var endPinEl = M.bkmCreation._createBkmEndPin(id);

		var startNodeTree = eval('['+this.getAttribute('data-startNodeTree')+']');
		var endNodeTree = eval('['+this.getAttribute('data-endNodeTree')+']');
		var startOffset = parseInt(this.getAttribute('data-startOffset'));
		var endOffset = parseInt(this.getAttribute('data-endOffset'));

		// mapper module, always end node first not to mess the structure
		var endNode = M.bkmMapper._findNodeInDOM(endNodeTree, M.bkmCreation.chapterRootNode);
		M.bkmMapper._insertIntoTextNode(endNode, endPinEl, endOffset);		
		var startNode = M.bkmMapper._findNodeInDOM(startNodeTree, M.bkmCreation.chapterRootNode);
		M.bkmMapper._insertIntoTextNode(startNode, startPinEl, startOffset);

		// TO-DO: scroll a little bit more behind navbar
		startPinEl.focus();
		return false;

	},

	// from a back to chapter linke. Dinamically create end point to return to
	// ============================
	accessBookmarksEnd: function(){
		M.bkmCreation._clearChapterTextFromPins();

		var id = this.getAttribute('href').substring(1);
		// TO-DO: check if pin exists already

		var endPinEl = M.bkmCreation._createBkmEndPin(id);

		var endNodeTree = eval('['+this.getAttribute('data-endNodeTree')+']');
		var endOffset = parseInt(this.getAttribute('data-endOffset'));

		// mapper module, always end node first not to mess the structure
		var endNode = M.bkmMapper._findNodeInDOM(endNodeTree, M.bkmCreation.chapterRootNode);
		M.bkmMapper._insertIntoTextNode(endNode, endPinEl, endOffset);		

		endPinEl.focus();
	},

	// ============================
	_clearChapterTextFromPins:function(){
		// remove all the marks that this script did to a chapter text, so that the tekst positions are correct
		Y.all('.'+this.bkmStartPinClass+', .'+this.bkmEndPinClass).each(function(bkm){
			var parent = bkm.get('parentNode').getDOMNode();
			parent.removeChild(bkm.getDOMNode());
			parent.normalize();
		});
	},

	// ============================
	_createBkmStartPin:function(bkm_id, bkm_name){
		var randomID = bkm_id;
		var newA = document.createElement("a");
		newA.setAttribute('href', '#'+randomID);
		newA.setAttribute('id', randomID);
		newA.setAttribute('class', this.bkmStartPinClass);
		newA.setAttribute('aria-label', M.util.get_string('aria-start-pin', 'block_bookmarks') + ' ' + bkm_name);
		newA.innerHTML = '<mark aria-hidden="true"> [ </mark>'; // TO-DO: this could be aria titled into something useful

		// destroy pin on blur
		newA.onblur = this._bookmarkPinBlur;
		return newA;
	},

	// ============================
	_createBkmEndPin:function(bkm_id){
		var randomID = bkm_id;
		var newA = document.createElement("a");
		newA.setAttribute('href', '#link_'+ randomID);
		newA.setAttribute('id', 'end_'+ randomID);
		newA.setAttribute('class', this.bkmEndPinClass);
		newA.setAttribute('aria-label', M.util.get_string('aria-end-pin', 'block_bookmarks'));
		newA.innerHTML = '<mark aria-hidden="true"> ] </mark>';

		// destroy pin on blur
		newA.onblur = this._bookmarkPinBlur;
		return newA;
	},

	// ============================
	_bookmarkPinBlur: function(e){
		var parent = this.parentNode
		parent.removeChild(this);
		parent.normalize(); // merge separate text nodes back together
	},

	// ============================
	_storeBookmarkToDatabase: function(dbData){
		// TO-DO: implement loader icon here, use the code from accessibility_block
		// TO-DO: make better use experience on failure
		Y.io(this.DB_PATH, {
			data: dbData,
			method: 'post',
			on: {
				success: function(id, o) {
					//debugger;
					//alert('SUCCESS')
					//M.block_bookmarks.show_message(M.util.get_string('saved', 'block_bookmarks'));
					//setTimeout("M.block_bookmarks.show_message('')", 5000);
				},
				failure: function(id, o) {
					// TO-DO: back to chapter link should be updated with failure message
					Y.one('.block_bookmarks').setStyle('background', 'red'); // just fyi
					alert('FAILED! Please contact the administrator. You refresh refresh a page now.')
					//alert(M.util.get_string('jsnosave', 'block_bookmarks')+' '+o.status+' '+o.statusText);
				},
				start: this.show_loading,
				end: this.hide_loading
			}
		});
	},

	// get start and end node, their tree path and offsets of selection within those nodes
	// ============================
	_getSelectionPosition: function(){
		//debugger;
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
				var userSelection = document.getSelection(); // why not window.getSelection()??? bug? It works anyway...
				
				// conversion: We need Range object, NOT Selection object
				var rangeObject = M.bkmCreation._getRangeObject(userSelection);
				
				// There is also YUI way but doesn't work in IE<11
				//var editor = new Y.EditorSelection(); editor.anchorTextNode;

				// populate data from Range object
				selection.startNode = rangeObject.startContainer;
				selection.endNode = rangeObject.endContainer;
				selection.startOffset = rangeObject.startOffset;
				selection.endOffset = rangeObject.endOffset;
				//rangeObject.commonAncestorContainer				
				
			}
			// FOR IE (this must come last!!)
			else if (document.selection) { 
				alert(M.util.get_string('browser-unsupported', 'block_bookmarks'));
				// TO-DO: implement this if possible (IE9+ should work anyway)
				// the closest what works in IE8 = http://stackoverflow.com/questions/1223324/selection-startcontainer-in-ie
				return null;				
			}	// else userSelection = document.getSelection(); // no need for this...
			
		} 
		catch (err) {
			//alert(err)
			Y.one('.block_bookmarks').setStyle('background', 'red'); // just fyi
			//alert(' ????? It seems your browser cannot be used for creating user bookmarks. Please use a moderm browser. Error Message: ' + err)
		}

		// TO-DO: check if selection is within book chapter?
		// TO-DO: M.bkmCreation.chapterRootNode return null;

		// populate node types
		selection.startNodeType = null;
		selection.endNodeType = null;

		// populate node trees
		selection.startNodeTree = M.bkmCreation._calculateNodelistTree(selection.startNode);
		selection.endNodeTree = M.bkmCreation._calculateNodelistTree(selection.endNode);

		return selection;
	},


	// ============================
	_getRangeObject: function (selectionObject) {
		if (selectionObject.getRangeAt)
			return selectionObject.getRangeAt(0);
		else { // Safari!
			var range = document.createRange();
			range.setStart(selectionObject.anchorNode,selectionObject.anchorOffset);
			range.setEnd(selectionObject.focusNode,selectionObject.focusOffset);
			return range;
		}
	},


	// determine depth of parent elements (specific location path as a node tree of parents)
	// ============================
	_calculateNodelistTree: function(node){
		// node is always text node? It must be...
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
	},

	// ============================
	_generateUniqueId: function(length){
		var ID = M.bkmCreation.ID_PREFIX;
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

		for( var i=0; i < length; i++ )
		{
			ID += possible.charAt(Math.floor(Math.random() * possible.length));
		}

		// check if we have generated this key already?
		if(M.bkmCreation.USED_IDs.indexOf(ID) >= 0) ID = M.bkmCreation._generateUniqueId(length);
		else M.bkmCreation.USED_IDs.push(ID);

		return ID;
	}	,

	show_loading: function(){
		this.transactionsCount++;
		Y.one('.loader-icon').setStyle('display', 'block');
		Y.one('.block_bookmarks .content').setStyle('opacity', '0.2');	
	},
	hide_loading: function(){
		if(this.transactionsCount < 0) this.transactionsCount--;
		else this.transactionsCount = 0; // prevention if count would end up to less than 0

		if(this.transactionsCount == 0){
			Y.one('.loader-icon').setStyle('display', 'none');
			Y.one('.block_bookmarks .content').setStyle('opacity', '1');	
		}
	},


}


//IE <9 support
if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}
