var spans_array = $("#select-trades");
var spans_array_copy = spans_array.clone();

var sort_attr = $('input[name=sort]:checked', '#sort_traders').val();
$('#sort_traders input').change(function() {
    sort_attr = $('input[name=sort]:checked', '#sort_traders').val();
    // Clone for clean resort without group separators
    spans_array.html(spans_array_copy.html());
    resort(spans_array, sort_attr);
});

// Default first run
resort(spans_array, sort_attr);

function resort(spans_array, sort_attr) {
    window.ts_iter = 0;
    window.ts_last = 'undef';
    var is_separater = false;
    var sort_result;

    if (sort_attr == 'note') {
        sort_result = spans_array.find('span').sort(function(a, b) {
            if (a.getAttribute('note') === '') return 1;    // For empty nickname
            if (b.getAttribute('note') === '') return -1;    // For empty nickname
            return -a.getAttribute('note').toLowerCase().localeCompare(b.getAttribute('note').toLowerCase());
        });

        sort_result.each(function() {
            var elem = $(this);
            elem.remove();
            window.ts_new = elem.attr('note');
            is_separater = window.ts_last.toLowerCase().localeCompare(window.ts_new.toLowerCase()) != 0;
            if (is_separater)
                $(elem).attr('group', 'group_' + (window.ts_iter + 1));
            else
                $(elem).attr('group', 'group_' + window.ts_iter);
            var sep_content = '<label class="ts-separator"><input type="checkbox" name="group_' + (window.ts_iter + 1) + '"/>' + elem.attr('note') + '</label>';
            var sep_elem = $(sep_content);
            if (is_separater) {
                window.ts_iter++;
                $(sep_elem).appendTo("#select-trades");
            }
            $(elem).appendTo("#select-trades");
            window.ts_last = window.ts_new;
        });
    } else if (sort_attr == 'nickname') {
        sort_result = spans_array.find('span').sort(function(a, b) {
            if (a.getAttribute('nickname') === '') return 1;    // For empty nickname
            if (b.getAttribute('nickname') === '') return -1;    // For empty nickname
            return a.getAttribute('nickname').toLowerCase().localeCompare(b.getAttribute('nickname').toLowerCase());
        });

        sort_result.each(function() {
            var elem = $(this);
            elem.remove();
            window.ts_new = elem.attr('nickname');
            is_separater = window.ts_last.toLowerCase().localeCompare(window.ts_new.toLowerCase()) != 0;
            if (is_separater)
                $(elem).attr('group', 'group_' + (window.ts_iter + 1));
            else
                $(elem).attr('group', 'group_' + window.ts_iter);
            var sep_content = '<label class="ts-separator"><input type="checkbox" name="group_' + (window.ts_iter + 1) + '"/>' + elem.attr('nickname') + '</label>';
            var sep_elem = $(sep_content);
            if (is_separater) {
                window.ts_iter++;
                $(sep_elem).appendTo("#select-trades");
            }
            $(elem).appendTo("#select-trades");
            window.ts_last = window.ts_new;
        });
    } else {
        sort_result = spans_array.find('span').sort(function(a, b) {
            return a.getAttribute('value').toLowerCase().localeCompare(b.getAttribute('value').toLowerCase());
        });

        sort_result.each(function() {
            var elem = $(this);
            elem.remove();
            $(elem).appendTo("#select-trades");
        });
    }

    $("label.ts-separator input:checkbox").change(function () {
        $("#select-trades span[group='" + $(this).attr('name') + "']").attr('class', $(this)[0].checked ? 'selected' : '' );
    });

    // Moved\edited from network-sync.js for correct work after sort selection
    $('#select-trades').selectable();

}

// Moved\edited from network-sync.js for correct work after sort selection
$('#select-trades').selectable();