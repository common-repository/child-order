(function ($) {
  var saveChildOrder = function (event, ui) {
    var list = $("#child_order_list");
    var ids = [];
    list.find("li").each(function () {
      ids.push($(this).attr('data-post-id'));
    });
    var post_id = list.attr('data-post-id');
    var data = {
      'action': 'child_order_save',
      'post': post_id,
      'order': ids.join(",")
    };
    list.find('input').each(function () {
      var input = $(this);
      var value = input.val();
      if (input.is(':checkbox') && !input.is(':checked')) value = '';
      data[input.attr('name')] = value;
    });
    list.find('select').each(function () {
      var name = $(this).attr('name');
      var value = $(this).find('option:selected').val();
      data[name] = value;
    });
    $.post(ajaxurl, data);
  };

  $(document).ready(function () {
    var list = $("#child_order_list");
    if (list && list.sortable) {
      list.sortable({
        'revert': 100,
        'cursor': 'pointer',
        'update': saveChildOrder,
      });
      list.find("input, select").change(saveChildOrder);
      $("#child_order_list, #child_order_list li").disableSelection();
    }
  }); 
})(jQuery);
