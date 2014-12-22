M.bkmMapper = {
	// KEEP IN MIND: chapter root node is obtained from M.bkmCreation.chapterRootNode

	init: function(Y) {
		// 01. Get all bookmarks for this user in this chapter
		/*var allBookmarks = Y.all('.bookmarks_listing a');

		allBookmarks.each(function(bookmark){
			// 02. parse bookmark data
			// TO-DO try...catch block
			var startNodeTree = eval('['+bookmark.getAttribute('data-startNodeTree')+']');
			var endNodeTree = eval('['+bookmark.getAttribute('data-endNodeTree')+']');
			var startOffset = parseInt(bookmark.getAttribute('data-startOffset'));
			var endOffset = parseInt(bookmark.getAttribute('data-endOffset'));

			// 03. find related DOM nodes
			var startNode = this._findNodeInDOM(startNodeTree, M.bkmCreation.chapterRootNode);
			// jako je vazno da insert bude odmah nakon dohvacanje elementa jer inace se moze desiti da se ne uhvati wrapper
			this._insertIntoTextNode(startNode, startOffset);
			var endNode = this._findNodeInDOM(endNodeTree, M.bkmCreation.chapterRootNode);

			// 04. link block bookmarks to chapter locations
			this._insertIntoTextNode(endNode, endOffset);

		}, this);*/

		

			

			
		



	},


	
	_findNodeInDOM: function (positionTree, rootEl){
		var rootNode = rootEl;
		var tempParent = rootNode;
		for(var ni=0, nlen=positionTree.length; ni<nlen; ni++){
			tempParent = tempParent.childNodes.item(positionTree[ni]);  
		}
		return tempParent;
	},

	_insertIntoTextNode: function (textNode, newNode, offset) {
		if(textNode.nodeType == 3) { // 3 => a Text Node
			var strSrc = textNode.nodeValue; // for Text Nodes, the nodeValue property contains the text
			if(offset >= 0) {
				var fragment = document.createDocumentFragment();

				// first part of original content
				if(offset > 0)	fragment.appendChild(document.createTextNode(strSrc.substr(0, offset)));

				// custom part (insertion)
				fragment.appendChild(newNode);

				// second part of original content
				if(offset < strSrc.length) fragment.appendChild(document.createTextNode(strSrc.substr(offset)));

				// swap old node for a new one
				textNode.parentNode.oldContent = textNode;
				textNode.parentNode.replaceChild(fragment, textNode);
			}
			return true;
		}
		else return false;
	}

	/*
	_insertIntoTextNode: function (textNode, offset) {
		debugger;
		if(offset < 0) return;

		var wrapper;
		if(textNode.nodeType == 3) { // 3 => a Text Node
			// wrapper is required so that a parent node preserves child nodes length
			wrapper = document.createElement('span');
			wrapper.isTextNodeWrapper = true;
			wrapper.originalContent = textNode.nodeValue;
			wrapper.bookmarksPositions = [];			
		}
		else if(textNode.hasOwnProperty('isTextNodeWrapper')){
			wrapper = textNode;
		}

		wrapper.bookmarksPositions.push(offset);
		wrapper.bookmarksPositions.sort();

		// clear wrapper innerHTML
		while (wrapper.firstChild) wrapper.removeChild(wrapper.firstChild);

		var endPosition = 0;  
		var startPosition = 0;          
		for(i=0, len=wrapper.bookmarksPositions.length; i<len; i++){         
			
			startPosition = endPosition;
			endPosition = wrapper.bookmarksPositions[i];
			
			// first part of original content
			if( endPosition > startPosition) wrapper.appendChild(document.createTextNode(wrapper.originalContent.substring(startPosition,endPosition)));
			
			// custom part (insertion)
			var mark = document.createElement("a");
			mark.setAttribute('href', '#');
			mark.setAttribute('name', 'newbookmark');
			mark.setAttribute('id', 'newbookmark');
			//mark.appendChild(document.createTextNode("XXXX"));
			wrapper.appendChild(mark);
		}

		// append the rest
		startPosition = endPosition;
		endPosition = wrapper.originalContent.length-1;
		if( endPosition > startPosition) wrapper.appendChild(document.createTextNode(wrapper.originalContent.substring(startPosition)));

		// swap old node for a new one
		textNode.parentNode.replaceChild(wrapper, textNode);
	}*/

}