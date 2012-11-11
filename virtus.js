iphone = {
	init: function() {
		if( virtues.view != 'iphone' )
		{
			return;
		}
				
		iphone.updateLayout();
		setInterval(iphone.updateLayout, 500);
		setTimeout(iphone.hideToolbar, 100);
		
		// alert(window.innerHeight);
	},
	hideToolbar: function() {
		window.scrollTo(0, 1);
	},
	updateLayout: function() {
		if (window.innerWidth != iphone.width) {
			iphone.width = window.innerWidth;
			iphone.height = window.innerHeight;
			iphone.orientation = (iphone.width == 320) ? "profile" : "landscape";
			
			itemHeight = iphone.height / 7;
			// itemHeight = 53;
						
			$('#virtues li, #virtues li a, #virtues li span').height( itemHeight );
			$('#virtues li a').css('line-height', itemHeight + 'px');

			// $('#virtues').width( iphone.width );
			
			iphone.hideToolbar();					
		}
		
		
	},
	fetch: function() {
		
		// return;
		
		// alert( refresh_url );
// 		
		$.ajax({
			url: refresh_url,
			data: {
				'meh': 'meh'
			},
			contentType: 'application/x-www-form-urlencoded',
			dataType: 'html',
			cache: false,
			success: function(data) {
				// alert( data );
				
				// alert( data );
				$('#page').html( data );
				
				virtues.init();
				iphone.init();
				
			}
		});
		
		// consoe.log( $('html') );
	}
}

virtues = {
	init: function() {
		
		if( $('body').hasClass('iphone') )
		{
			virtues.view = 'iphone';
			
			virtues.togglers = $('#virtues li.virtue a');
			virtues.comments = $('input');
												
		}
		else
		{
			virtues.view = 'month';
			
			virtues.togglers = $('table td.virtue a');
			virtues.comments = $('table td.comment input');
			
			virtues.ajax = $.manageAjax({manageType: 'abortOld', maxReq: 0}); 
		}
				
		
		
		virtues.togglers.click( function() {
			virtues.toggle( $(this).parent() );
			
			return false;
		});
		
		virtues.comments.keyup( function() {
			virtues.set_comment( $(this).parents('tr').attr('date'), $(this).val() );
		});
		
	},
	toggle: function( virtue ) {
		
		if( virtues.view == 'iphone')
		{			
			if( $('.part.true', virtue).length < $('.part', virtue).length )
			{
				// More to set
				total = $('.part.true', virtue).length + 1;
				
				$('.part:not(.true)', virtue).first().text( 'true' ).removeClass( 'false ').addClass( 'true');
				
			}
			else
			{
				total = 0;
				
				$('.part', virtue).text( 'false' ).removeClass( 'true ').addClass( 'false');
			
			}
			
			key = virtue.attr('virtue');
			date= $('h1#date').attr('date');
						
		}
		else
		{
			if( virtue.text() == 'true' )
			{
				$('a', virtue).text( 'false' );
				virtue.removeClass('true');
				virtue.addClass('false');
			}
			else
			{
				$('a', virtue).text( 'true' );
				virtue.addClass('true');
				virtue.removeClass('false');
			}

			total = $('.virtue.true.' + virtue.attr('virtue'), virtue.parent() ).length;
			date = virtue.parent().attr('date');
			
			key = virtue.attr('virtue');
		}
		
		
		
		virtues.set( date, key, total);
		
	},
	set_comment: function( date, comment ) {
		$.ajax({
			url: url,
			dataType: 'json',
			data: {
				'date': date,
				'comment': comment
			},
			success: function(data) {
			}
		});
	},
	set: function( date, virtue, status ) {
		
		$.ajax({
			url: url,
			dataType: 'json',
			data: {
				'date': date,
				'virtue': virtue,
				'status': status
			},
			success: function(data) {
				$('td.week.points', $('tr[date=' + date + ']').parent()).text( data['week'] );
				$('h2').text( data['month'] + ' points' );
				
				if( data['record-month'] != $('#monthrecord td').text() )
				{
					$('#monthrecord td').text( data['record-month'] );
					humanMsg.displayMsg( "New monthly record set." );
				}
				
				if( data['record-week'] != $('#weekrecord td').text() )
				{
					$('#weekrecord td').text( data['record-week'] );
					humanMsg.displayMsg( "New weekly record set." );
				}
				
				if( data['record-day'] != $('#dayrecord td').text() )
				{
					$('#dayrecord td').text( data['record-day'] );
					humanMsg.displayMsg( "New daily record set." );
				}
			}
		});
		
		
		
	}
}

$(document).ready(function() {
	if( $('body').hasClass('iphone') )
	{
		iphone.fetch();
					
	}
	else
	{
		virtues.init();
	}
});

var cacheStatusValues = [];
cacheStatusValues[0] = 'uncached';
cacheStatusValues[1] = 'idle';
cacheStatusValues[2] = 'checking';
cacheStatusValues[3] = 'downloading';
cacheStatusValues[4] = 'updateready';
cacheStatusValues[5] = 'obsolete';

var cache = window.applicationCache;
cache.addEventListener('cached', logEvent, false);
cache.addEventListener('checking', logEvent, false);
cache.addEventListener('downloading', logEvent, false);
cache.addEventListener('error', logEvent, false);
cache.addEventListener('noupdate', logEvent, false);
cache.addEventListener('obsolete', logEvent, false);
cache.addEventListener('progress', logEvent, false);
cache.addEventListener('updateready', logEvent, false);

function logEvent(e) {
    var online, status, type, message;
    online = (navigator.onLine) ? 'yes' : 'no';
    status = cacheStatusValues[cache.status];
    type = e.type;
    message = 'online: ' + online;
    message+= ', event: ' + type;
    message+= ', status: ' + status;
    if (type == 'error' && navigator.onLine) {
        message+= ' (prolly a syntax error in manifest)';
    }
    console.log(message);
}

window.applicationCache.addEventListener(
    'updateready',
    function(){
        window.applicationCache.swapCache();
        console.log('swap cache has been called');
    },
    false
);

setInterval(function(){cache.update()}, 10000);

