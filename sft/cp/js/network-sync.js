var selected_sites = null;
var selected_settings = null;
var selected_trades = null;
var in_progress = false;
var total_sites = 0;
var sync_errors = false;
//////////////////////////////////////
var del_errors = false;

$(function()
{
    // Make selectable
    $('div.selectable-container').selectable();


    // Select all settings
    $('#select-settings-all')
    .click(function()
    {
        $('#select-settings').selectable('all');
    });


    // Deselect all settings
    $('#select-settings-none')
    .click(function()
    {
        $('#select-settings').selectable('none');
    });


    // Select all trades
    $('#select-trades-all')
    .click(function()
    {
        $('#select-trades span').attr('class', 'selected');
        $("#select-trades input[type='checkbox']").prop('checked', true);
        /*$('#select-trades').selectable('all');
        $('.selectable-trades-checkboxes input[type="checkbox"]').attr('checked', true);*/
    });


    // Deselect all trades
    $('#select-trades-none')
    .click(function()
    {
        $('#select-trades span').attr('class', '');
        $("#select-trades input[type='checkbox']").prop('checked', false);
        /*$('#select-trades').selectable('none');
        $('.selectable-trades-checkboxes input[type="checkbox"]').attr('checked', false);*/
    });


    //!!!!!!!!!!!!!!!!!!
    // Select trades by category
    //$('.selectable-trades-checkboxes input[type="checkbox"]')
    //.click(function()
    //{
    //    console.log('line 58');
    //
    //    var $checkboxes = $(this).parents('div.selectable-checkboxes-container').find('input:checked');
    //    var categories = [];
    //
    //    $checkboxes.each(function(i, cb)
    //    {
    //        categories.push($(cb).attr('value'));
    //    });
    //
    //    $('#select-trades')
    //    .selectable(
    //        'multi_matching',
    //        $(this).attr('name'),
    //        categories
    //    );
    //});



    // Select all sites
    $('#select-sites-all')
    .click(function()
    {
        $('#select-sites').selectable('all');
        $('.selectable-sites-checkboxes input[type="checkbox"]').prop('checked', true);
    });


    // Deselect all sites
    $('#select-sites-none')
    .click(function()
    {
        $('#select-sites').selectable('none');
        $('.selectable-sites-checkboxes input[type="checkbox"]').prop('checked', false);
    });




    //!!!!!!!!!!!!!!!!!!!!!!
    // Select sites by category or owner
    $('.selectable-sites-checkboxes input[type="checkbox"]')
    .click(function()
    {
        $('#select-sites')
        .selectable($(this).prop("checked") ? 'matching' : 'unmatching', $(this).attr('name'), $(this).attr('value'));
    });






    // Syncing trades, show trade selection
    $('span[value="trades"]')
    .bind('selected', function(evt, is_selected)
    {
        if( is_selected )
        {
            $('#sync-trades').show();
        }
        else
        {
            $('#sync-trades').hide();
        }
    });


    // Start sync
    $('img[src="images/sync-32x32.png"]')
    .click(function()
    {
        if( in_progress )
        {
            alert('Syncing is currently in progress!');
            return;
        }

        selected_sites = $('#select-sites').selectable('selected');
        total_sites = selected_sites.length;
        if( total_sites < 1 )
        {
            alert('Please select at least one site to sync');
            return;
        }

        selected_settings = $('#select-settings').selectable('selected');
        if( selected_settings.length < 1 )
        {
            alert('Please select at least one setting to sync');
            return;
        }

        selected_trades = $('#select-trades').selectable('selected');

        //console.log(selected_trades);

        startSync();
    });


    //////////////////////////////////////////////////
    // Start del
    $('#button-save[value="Delete Trades"]')
        .click(function()
        {

            // Local delete condition
            if ( $('input#flag_delete_from_network').attr('value') === "0" ) {
                return;
            }

            if( in_progress )
            {
                alert('Network deleting is currently in progress!');
                return;
            }

            // Sites of network
            selected_sites = $('#select-sites span').map(function() {
                return this.innerText;
            });
            selected_sites = jQuery.makeArray( selected_sites );
            total_sites = selected_sites.length;
            if( total_sites < 1 )
            {
                alert('Looks like is no sites in Network. Local trader Deletion.');
                return;
            }

            selected_settings = ["trades-delete"];

            selected_trades = $('#del-domains-list span').map(function() {
                return this.innerText;
            });
            selected_trades = jQuery.makeArray( selected_trades );

            startDelete(selected_trades);
        });
    //////////////////////////////////////////////////

    //selected_sites = $('#select-sites').selectable('selected');
    //
    //selected_settings = $('#select-settings').selectable('selected');
    //
    //console.log(selected_sites);
    //console.log(selected_settings);

    //selected_sites = $('#select-sites span').map(function() {
    //    return this.innerText;
    //});
    //selected_sites = jQuery.makeArray( selected_sites );
    //console.log(selected_sites);


});


function startSync()
{
    total_sites = selected_sites.length;

    $('#sync-progress').show();
    $('#sync-current').show();
    $('#sync-complete').html('');
    $('#sync-dump').html('');
    $('#sync-num-total').html(total_sites);

    //$('fieldset.sync-hide').hide();

    sync_errors = false;
    in_progress = true;
    syncNext(true);

    //window.scrollTo(0, 0);
    $('html, body').animate({ scrollTop: 0 }, 'slow');
}


//////////////////////////////////////
function startDelete(trades_delete)
{
    total_sites = trades_delete.length;



    del_errors = false;
    in_progress = true;
    delNext(true);
}
function delNext(cache)
{
    if( selected_sites.length > 0 )
    {
        var domain = selected_sites.shift();

        $.ajax({
            async: true,
            data: 'r=_xNetworkSiteDelete&domain=' + domain + (cache ? '&cache=1&settings=' + encodeURI(selected_settings.join(',')) + '&trades=' + encodeURI(selected_trades.join(',')) : ''),
            success: function(data)
            {
                switch(data[JSON.KEY_STATUS])
                {
                    case JSON.STATUS_SUCCESS:
                        $('#sync-complete')
                            .prepend('<div class="sync-success"><span>' + domain + '</span> sync successful!</div>');
                        break;

                    case JSON.STATUS_WARNING:
                        del_errors = true;
                        $('#sync-complete')
                            .prepend('<div class="sync-failure"><span>' + domain + '</span> deletion failed: ' + data.response + '</div>');
                        break;
                }
            },
            complete: function()
            {
                delNext(false);
            }
        });
    }
    else
    {
        in_progress = false;



        if( !del_errors )
        {
            //$('#sync-progress').hide();
            $.growl('Network deletion has been completed successfully!');
        }
        else
        {
            $.growl.warning('Network deletion has been finished, however some errors were encountered.  See the Results output for details.');
        }
    }
}





function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function makeTitle(domain) {
    var words = 'the,with,site,yuck,yep,big,like,fucked,fuck,ladies,lady,silver,infinite,clips,clip,lovely,grandmas,grandma,omas,oma,oldest,old,elder,geronto,lewd,sexy,sex,porno,porn,prn,hot,hd,hq,vaginas,vagina,fast,hunter,girls,girl,pussy,senior,adult,tube,xxx,vids,videos,movs,movies,pics,pictures,photos,photo,films,film,sluts,slut,fat,plump,accident,african,amateur,anal,anime,arab,armpit,asian,ass,audition,aunt,babe,backroom,bareback,bath,bathroom,bbc,bbw,bdsm,beach,beauty,clit,cock,tits,bikini,bisexual,black,blonde,blowjob,bondage,boots,bottle,brazil,bride,british,brunette,bukkake,bulgarian,bus,cameltoe,car,cartoon,casting,catfight,caught,celebrity,cfnm,cheating,cheerleader,chinese,chubby,cinema,classic,clit,close,up,clothed,club,college,compilation,condom,contest,cougar,couple,cousin,creampie,compilation,crossdresser,cuckold,cumshot,cute,czech,dance,danish,deepthroat,diaper,dildo,doctor,dogging,doll,domination,dorm,penetration,downblouse,dress,drugged,drunk,dutch,ebony,electro,emo,erotic,exam,facesitting,facial,farm,farting,fat,feet,femdom,fetish,ffm,filipina,finnish,fisting,flashing,flexible,food,footjob,foursome,french,funny,futanari,gagging,game,gangbang,gay,german,girdle,girlfriend,glasses,gloryhole,gloves,goth,grandpa,granny,grannies,greek,groped,group,gym,gyno,hairy,handjob,heels,hidden,homemade,hooker,hotel,housewife,hungarian,husband,india,indian,indonesian,insertion,instruction,interracial,italian,japanese,jeans,jerking,kissing,kitchen,korean,lactating,ladyboy,latex,latina,leather,lesbian,lingerie,machine,maid,mask,massage,masturbation,mature,matur,mexican,midget,milf,milk,mmf,moms,mom,money,monster,muscle,natural,nipples,norwegian,nudist,nun,nurse,nylon,office,oil,orgasm,orgy,outdoor,pakistani,panties,pantyhose,party,penis,piercing,pissing,police,polish,pool,portuguese,pov,pregnant,prostate,prostitute,public,pump,reality,redhead,riding,romanian,rubber,russian,satin,sauna,screaming,secretary,seduced,selfsuck,serbian,shaving,shemale,shoejob,shoes,shower,shy,skinny,sleeping,smoking,socks,solarium,solo,spandex,spanish,spanking,sperm,sport,spy,squirt,stepmom,stewardess,stockings,stolen,strapon,strip,student,surprise,swallow,swedish,swimsuit,swinger,swiss,sybian,tall,tattoo,taxi,teacher,tease,teen,tentacle,thai,thong,threesome,tied,tight,titjob,toilet,toys,train,turkish,twins,ugly,uncle,underwater,uniform,upskirt,vibrator,vintage,voyeur,webcam,wedding,wet,whore,wife,wrestling,yoga';
    words = words.split(',').sort(function s(a, b){return b.length - a.length;});

    domain = domain.replace(/\.(.*)$/, '');
    domain = domain.replace('-', ' ');
    domain = domain.replace(/([0-9]+)/g, " $1 ");

    for (var i in words) {
        domain = domain.replace(words[i], ' ' + words[i] + ' ');
    }

    domain = domain.replace(/\s{2,}/g, ' ');
    domain = domain.trim();
    domain = domain.replace(/\s([a-z])\b/ig, "$1");

    domain = domain.split(' ');
    var title = '';
    for (var i in domain) {
        title += ' ' + ucfirst(domain[i]);
    }

    title = title.trim();

    return title;
}

//////////////////////////////////////


function syncNext(cache)
{
    if( selected_sites.length > 0 )
    {
        var domain = selected_sites.shift();

        $('#sync-site').html(domain);
        $('#sync-num-done').html(total_sites - selected_sites.length);

        $.ajax({
            async: true,
            data: 'r=_xNetworkSync&domain=' + domain + (cache ? '&cache=1&settings=' + encodeURI(selected_settings.join(',')) + '&trades=' + encodeURI(selected_trades.join(',')) : ''),
            success: function(data)
            {
                switch(data[JSON.KEY_STATUS])
                {
                    case JSON.STATUS_SUCCESS:
                        $('#sync-complete')
                        .prepend('<div class="sync-success"><span>' + domain + '</span> sync successful!</div>');
                        $('#sync-dump')
                            .prepend('http://www.' + domain + '/|' + makeTitle(domain) + "\n");
                        break;

                    case JSON.STATUS_WARNING:
                        sync_errors = true;
                        $('#sync-complete')
                        .prepend('<div class="sync-failure"><span>' + domain + '</span> sync failed: ' + data.response + '</div>');
                        break;
                }
            },
            complete: function()
            {
                syncNext(false);
            }
        });
    }
    else
    {
        in_progress = false;
        $('fieldset.sync-hide').show();
        $('#sync-current').hide();
        $('#select-sites-none').click();
        $('#select-settings-none').click();
        $('#select-trades-none').click();

        if( !sync_errors )
        {
            //$('#sync-progress').hide();
            $.growl('Syncing has been completed successfully!');
        }
        else
        {
            $.growl.warning('Syncing has been finished, however some errors were encountered. See the Results output for details.');
        }
    }
}

(function($)
{
    // Plugin definition
    $.fn.selectable = function(options)
    {
        switch(typeof options)
        {
            case 'string':
                return eval(options + '.apply(this, arguments);');

            case 'object':
            case 'undefined':
                this
                .children('span')
                .click(function()
                {
                    $(this)
                    .toggleClass('selected')
                    .trigger('selected', [$(this).hasClass('selected')]);
                });
                break;
        }
    };

    function selected()
    {
        var selected = new Array();

        this
        .children('span.selected')
        .each(function()
        {
            selected.push($(this).attr('value'));
        });

        return selected;
    }

    function multi_matching(fnc, attr, values)
    {
        this
        .children('span')
        .removeClass('selected')
        .each(function(i, span)
        {
            var categories = $(this).attr(attr);

            $.each(values, function(i, value)
            {
                if( categories.indexOf(',' + value + ',') != -1 )
                {
                    $(span).addClass('selected');
                    return false;
                }
            });
        });
    }

    function matching(fnc, attr, value)
    {
        this
        .children('span['+attr+'="'+value+'"]')
        .addClass('selected');
    }

    function unmatching(fnc, attr, value)
    {
        this
        .children('span['+attr+'="'+value+'"]')
        .removeClass('selected');
    }

    function all()
    {
        this
        .children('span')
        .removeClass('selected')
        .click();
    }

    function none()
    {
        this
        .children('span')
        .addClass('selected')
        .click();
    }

})(jQuery);
