(function($){
  function dragEnter(e) {
    $(e.target).addClass("dragOver");
    e.stopPropagation();
    e.preventDefault();
    return false;
  };

  function dragOver(e) {
    e.originalEvent.dataTransfer.dropEffect = "copy";
    e.stopPropagation();
    e.preventDefault();
    return false;
  };

  function dragLeave(e) {
    $(e.target).removeClass("dragOver");
    e.stopPropagation();
    e.preventDefault();
    return false;
  };

  $.fn.dropArea = function(){
    this.bind("dragenter", dragEnter).
         bind("dragover",  dragOver).
         bind("dragleave", dragLeave);
    return this;
  };
})(jQuery);

(function($){
  var insertAtCaret = function(value) {
    if (document.selection) { // IE
      this.focus();
      sel = document.selection.createRange();
      sel.text = value;
      this.focus();
    }
    else if (this.selectionStart || this.selectionStart == '0') {
      var startPos  = this.selectionStart;
      var endPos    = this.selectionEnd;
      var scrollTop = this.scrollTop;

      this.value = [
        this.value.substring(0, startPos),
        value,
        this.value.substring(endPos, this.value.length)
      ].join('');

      this.focus();
      this.selectionStart = startPos + value.length;
      this.selectionEnd   = startPos + value.length;
      this.scrollTop      = scrollTop;

    } else {
      throw new Error('insertAtCaret not supported');
    }
  };

  $.fn.insertAtCaret = function(value){
    $(this).each(function(){
      insertAtCaret.call(this, value);
    })
  };
})(jQuery);


$(function() {
 	$(".double a.button").click(function(){  
    	$(".double a.button").removeClass("checked");  
		$("input.RadioClass").attr("checked",null);

		$(this).prev("input.RadioClass").attr("checked","checked");
	    $(this).addClass("checked");
	});
	
	$(".remove").click(function(){
		var answer = confirm('Are you sure?');
		return answer;
	});

     // preview in iframe on the edit page
	$("a.button.preview").click(function(e){
		// does the iframe exist already?
		if ($("iframe.preview").length) {                
               // stop the event propogation
		     e.preventDefault();
               // display the preview pane
               $("div.preview").fadeIn(500);
		} 
	});

     // close preview
	$("a.close").click(function(e){
          e.preventDefault();
          $("div.preview").fadeOut(500);
     });

	$('body').dropArea();

	$('body').bind('drop', function(e){
	  e.preventDefault();
	  e = e.originalEvent;

	  var files = e.dataTransfer.files;

	  for (var i=0; i < files.length; i++) {
	    // Only upload images
	    if (/image/.test(files[i].type)) {
	      createAttachment(files[i]);
	    }
	  };
	});

});