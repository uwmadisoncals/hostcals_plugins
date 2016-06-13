<?php global $SimpleContentTemplates; ?>
<div class="wrap">
	<h2>Simple Content Templates Help</h2>

	<h3>PHP QuickStart</h3>
	<h4>It's easy to include PHP anywhere in your template. The following guidelines should give you some ideas.</h4>
	<p>You can use PHP however you desire to output dynamic data. This can be as simple as a date or as complex as a database query. In our organization, we title each new post with the current date.</p>
	<p>So, for our title template we use the following:</p>

	<pre>&lt;?PHP echo date(&quot;l&quot;). &quot; - &quot;.date(&quot;m/j/y&quot;); ?&gt;</pre>
	<p>This outputs something like:</p>
	<pre>Sunday - 10/4/09</pre>
	<p>Depending on the day, obviously.</p><p>You can use whatever mixture of HTML and
	PHP you desire. There are really no limits. So, for example,if you wanted to include a dynamic greeting in the body of your post, you could use something like this:</p>
	<pre>Greetings, friend.&nbsp; Today is &lt;?PHP echo date(&quot;l&quot;); ?&gt;.</pre>
	<p>Which would output:</p>
	<pre>Greetings, friend. Today is Sunday.</pre>
	<p>That's really all there is to it, but here are some things to think about:</p>
	<ol><li>PHP is executed when the template is insert into a new post. Not
		when the post is saved, updated, or viewed. </li>
		<li>Because all PHP is executed before the template is inserted, it
		is not possible to use this plugin with a PHP in post plugin (for
		live, dynamic content).&nbsp; Some have found Custom Field Template
		to suffice for this. (Linked to above)</li>
		<li>The title is plain text. This is a WordPress limitation.</li>
	</ol>

	<div style="width: 800px">
		<h4 style="margin-top: 40px">Need More?</h4>
		<img class="alignright" src="http://cgd.io/wp-content/uploads/edd/2014/02/logo2-300x300.png" style="width:300px" />
		<p><b>Go pro!</b> If you need support for custom post templates, taxonomies, featured images, etc, check out our premium plugin <a href="<?php echo $SimpleContentTemplates->bpt_url; ?>">Advanced Content Templates</a>.</p>

		<p>Here are a few of the features:</p>
		<ul style="list-style: disc; margin-left: 20px;">
			<li>Custom Post Types</li>
			<li>Categories, Tags, and other taxonomies</li>
			<li>Features Images / Attachments</li>
			<li>Template Settings (for pages)</li>
			<li>Custom Fields</li>
			<li>And more!</li>
		</ul>

		<a class="button-primary" href="<?php echo $SimpleContentTemplates->bpt_url; ?>&utm_medium=HelpPage">Learn More</a>
	</div>
</div>