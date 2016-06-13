function handleTweets(tweets){
    var x = tweets.length;
    var n = 0;
    
    var element = document.getElementById('twitFetch');
    var html = '<ul>';
    while(n < x) {
      html += '<li>' + tweets[n] + '</li>';
      n++;
    }
    html += '</ul>';
    element.innerHTML = html;
    heightFix();
}

if ($.trim($("p[id^='cals_twitterfetcher_'],p:hidden").text()) != ''){
	
	
	
	var tf0 = $("p[id='cals_twitterfetcher_0']").text();
	var tf1 = $("p[id='cals_twitterfetcher_1']").text();
	var tf2 = $("p[id='cals_twitterfetcher_2']").text();
	var tf3 = $("p[id='cals_twitterfetcher_3']").text();
	var tf4 = $("p[id='cals_twitterfetcher_4']").text();
	var tf5 = $("p[id='cals_twitterfetcher_5']").text();
	var tf6 = $("p[id='cals_twitterfetcher_6']").text();
	
	twitterFetcher.fetch(tf0,'twitFetch',parseInt(tf1),tf2,tf3,tf4,'',tf5,handleTweets,tf6);
}