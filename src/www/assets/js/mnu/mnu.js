


$(document).ready(function() {
	mnu.init(false);
 });

var mnu = {
	isDebug: false,
	isOpen: false,
	isInited: false,
	init: function (showDebug) {
		//
		if(typeof showDebug === "undefined") {
			mnu.isDebug = false;
		} else {
			mnu.isDebug = showDebug;
			mnu.log('mnu.isDebug:'+showDebug);			
		}
		//
		if(mnu.isInited){
			mnu.log('mnu.isInited already inited');			
			return;
		}

		mnu.addSidebar();
		//
		mnu.addMenuItem("/","/assets/icon/home_w.png","Home");
		//
		$.ajax({
			dataType: "json",
			url: "/menu/json",
			success: function(data) {
				mnu.log('mnu.init.ajax: success');
				data.d.menu.items.forEach(function (r){
					if(r.type=="row"){
						mnu.addMenuItem(r.href,r.icon,r.title);
					}
				});
				//
				$('#mySidenav > div.loginbar > img').attr('src',data.d.auth.avatar);
				$('#mySidenav > div.loginbar > span').html(data.d.auth.username);
				//
				$('#mySidenav').mouseover(
					function(ev){ 
						mnu.openNav(); 
					}
				);
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				mnu.logXHRError(jqXHR, textStatus, errorThrown);
			}
		});	
		
		//Hide Event
		window.addEventListener("click", e => {
			mnu.log('mnu.init > window.click event');
		  	if(mnu.isOpen) {
		  		mnu.closeNav();
		  		mnu.isOpen = false;
				mnu.log('mnu.init > menu hidden');
		  	}
		});

	},
	addSidebar: function() {

		var username = '';
		var avatar = 'https://api.adorable.io/avatars/92/'+Math.random()+'.png';// '/assets/icon/user.png';

		var mnuhtml = '';
		mnuhtml += "<div class='sidenav' id='mySidenav'   >";
		mnuhtml += "<div class='sidenav-logo' ><img class='round-img' width='140' src='/assets/icon/logo_smsgate.png' /></div>";
		mnuhtml += "<div class='loginbar' ><img class='round-img' width='40' src='"+avatar+"' /><span class='login-name'>"+username+"</span>";
		mnuhtml += "<br><a style='font-size: 12px; display: none; position: relative;left: 50px;' href='/login/logout' >Logout</a></div>";
		mnuhtml += "<div class='sideHamb' id='mySideHamb' >&#9776;</div>";
		mnuhtml += "<div class='closebtn' id='mySideCloseBtn'><!-- &times; --></div>";
		mnuhtml += "<div class='homebar' >";
		mnuhtml += "<ul id='menu'>";
		mnuhtml += "</ul></div></div>";

		$('body').after(mnuhtml);
		$('#mySideHamb').mouseover(function() { 
			mnu.log('mnu.mySideHamb mouseover');
			mnu.openNav();
		});

		$('#mySideCloseBtn').click(function() {
			mnu.log('mnu.mySideCloseBtn click');
			mnu.closeNav();
		});
	},
	addMenuItem: function (href,icon,title){
		var img = '/assets/icon/home_w.png';
		lihtml = '';
		lihtml += '<li style="margin: 5px 0 0 0"><a href="'+href+'" >';
		lihtml += '<img class="round-img" width="36" src="'+icon+'" alt="Home" /><span>'+title+'</span></a></li>';
		mnu.log('mnu.addMenuItem href:'+href+',icon:'+icon+',title:'+title);
		$('#menu').append(lihtml);
	},
	log: function(s){
		if(mnu.isDebug){
			console.log(s);
		}
	},
	logXHRError: function(jqXHR, textStatus, errorThrown) {
    	if (jqXHR.status === 0) {
			mnu.log('Not connect: Verify Network.');
		} else if (jqXHR.status == 404) {
			mnu.log('Requested page not found [404]');
		} else if (jqXHR.status == 500) {
			mnu.log('Internal Server Error [500].');
		} else if (textStatus === 'parsererror') {
			mnu.log('Requested JSON parse failed.');
		} else if (textStatus === 'timeout') {
			mnu.log('Time out error.');
		} else if (textStatus === 'abort') {
			mnu.log('Ajax request aborted.');
		} else {
			mnu.log('Uncaught Error: ' + jqXHR.responseText);
		}
	},
	openNav: function() {
		mnu.log('mnu.openNav');
		$('#mySidenav').delay(400);
	    $('#mySidenav').css('width','250px');
	    $('#mySideCloseBtn').css('display','block');
	    $('#mySideHamb').css('display','none');
  	    $('.login-name').show();
  	    $('#mySidenav > div.loginbar > a').fadeIn(1000).show();
  	    $('.sidenav-logo').show();
	    mnu.isOpen = true;
	},
	closeNav: function() {
		mnu.log('mnu.closeNav');
	    $('#mySidenav').css('width','60px');
	    $('#mySideCloseBtn').css('display','none');
	    $('#mySideHamb').css('display','block');
	    $('.login-name').hide();
	    $('#mySidenav > div.loginbar > a').hide();
	    $('.sidenav-logo').hide();
	    mnu.isOpen = false;
	}

}