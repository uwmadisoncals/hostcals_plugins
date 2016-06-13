<script type="text/javascript">
    jQuery(document).ready(function ($) {

//  $("#nav").sticky({topSpacing:0});

        $('a[href^="#"]').on('click', function (e) {
            e.preventDefault();

            var target = this.hash;
            var $target = $(target);

            $('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, 950, 'swing');
        });
    });


</script>
<style type="text/css">

    .sub-section {
        border: thin solid #91c6c0;
        border-radius: 15px;
        width: 80%;
        padding: 25px;
        margin-bottom: 15px;
    }

    div#wrapper {
        /*background: #ffffff;*/
        color: #313131;
        margin-top: 5px;
    }

    div#header {
        margin-right: 125px;
        background: #178dc4;
        border-radius: 0px 0px 50px 0px;
        padding: 20px;
        color: #fff;
    }

    div#main {
        height: 100%;
        background: blue;
    }

    div#nav {
        height: 100% !important;
        width: 15% !important;
        float: left !important;
    }

    div#content {
        float: right !important;
        width: 82% !important;
        /*background: #ffffff;*/
    }

    div#nav-sticky-wrapper {
        display: inline;
        margin-right: 0;
    }

    div#nav-sticky-wrapper.is-sticky {
        float: left;
    }

    p {
        padding-left: 25px;
        padding-right: 125px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    p.warning {
        color: #ff7777;
        font-style: italic;
    }

    div#header img#logo {
        float: left;
        width: 75px;
        padding-right: 15px;
    }


    h1#title{
        font-family: "Raleway", Verdana, sans-serif;
        font-weight: 100;
        font-size: 30px;    
    }
    h1#subtitle{

        font-size: 22px;
        font-weight: 100;
    }

    div#nav{
        border-right: 1px solid #f9f9f9;
        background: #DDDDDD;
    }
    h1.navheading{
        font-size: 14px;
        padding-top: 15px;
        margin-right: 0px;    
    }

    #nav ul {
        list-style: none;
        padding-left: 15px;
        padding-top: 10px;
    }

    #nav li {
        display: table;
        font-size: 12px;
        font-style: normal;
        background-color: #ffffff;
        color: #178dc4;
        width: 85%;
        border: thin solid #178dc4;
        border-radius: 5px;
        margin-bottom: 2px;
        text-align: center;
        min-height: 25px;
    }

    #nav li:hover {
        background-color: #178dc4;
        color: #ffffff;
        
        cursor: pointer;
    }
    #nav li:hover a{
        color: #fff;
    }
    a.navlink {
        font-size: 12px;
        color: #178dc4;
        text-decoration: none;
        display: table-cell;
        vertical-align: middle;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    ul {
        padding-left: 65px;
    }

    li {
        color: #91c6c0;
        /*font-style: italic;*/
        list-style: square;
    }

    footer {
        text-align: right;
        padding-right: 125px;
        padding-left: 25px;
        padding-top: 50px;
        padding-bottom: 50px;
    }

    i.fa {
        padding-right: 5px;
    }    
    em.pro{
        margin-left: 5px;
        font-size: 11px;
        color: #CC0000;
        
    }
</style>

<div id="wrapper">
    <div id="header">
        <img src="<?php echo SC_TEAM_URL ?>inc/img/smartcat_icon.png" alt="Smartcat" id="logo">
        <h1 id="title">Our Team Showcase</h1>
        <h1 id="subtitle">A WordPress Plugin by Smartcat</h1>
    </div>
    <div id="main">
        <div id="nav">

            <h1 class="navheading"><i class="fa fa-cube"></i>Setup</h1>
            <ul>
                <li><a href="#welcome" class="navlink">Welcome</a></li>
                <li><a href="#overview" class="navlink">Plugin Overview</a></li>
                <li><a href="#downloading" class="navlink">Downloading</a></li>
                <li><a href="#installing" class="navlink">Installing</a></li>
            </ul>
            <h1 class="navheading"><i class="fa fa-plug"></i>Plugin Usage</h1>
            <ul>	
                <li><a href="#include-sc" class="navlink">Including the Shortcode</a></li>
                <li><a href="#add-member" class="navlink">Add a New Member</a></li>
                <li><a href="#manage-members" class="navlink">Managing Members</a></li>
                <li><a href="#groups" class="navlink">Groups</a></li>
                <li><a href="#templates" class="navlink">Templates</a></li>
                <li><a href="#custom_templates" class="navlink">Custom Template</a></li>
                <li><a href="#view-settings" class="navlink">Settings</a></li>
                <li><a href="#sidebar-widget" class="navlink">Sidebar Widget</a></li>
            </ul>
            <h1 class="navheading"><i class="fa fa-question-circle"></i>Miscellaneous</h1>
            <ul>	
                <li><a href="#faq" class="navlink">FAQ</a></li>
                <ul>
                    </div>
                    <div id="content">
                        <h2 id="welcome">Welcome!</h2>
                        <p>
                            This is the documentation page for the 'Our Team Showcase' plugin, by Smartcat.
                            This document covers the details for both the free and the Pro versions of the plugin. <br>
                            Some of the items that only exist in the Pro version are labelled as such.
                        </p>

                        <h2 id="overview">Plugin Overview</h2>
                        <p>
                            Easily create, edit and display your staff, group or team on your website. This plugin has many team view and single view layouts, and comes with a settings menu through which you can manipulate the output easily.
                            The settings allow you to quickly and easily change the plugin's behaviour and appearance.
                        </p>

                        <h2 id="downloading">Downloading</h2>
                        <p>
                            After your purchase, you will receive an email receipt containing the a link
                            to download the plugin, 'Our Team Showcase Pro'. To start your download, click the link labelled
                            "smartcat_our_team", and your download will begin. You now have two options to install the plugin.
                        </p>

                        <h2 id="installing">Installing</h2>
                        <div class="sub-section">
                            <h3>Method One</h3>
                            <p>
                                If you decide to unzip / decompress the zip file, to install the plugin you must put the unzipped folder in 
                                your WordPress directory. Navigate to the root folder of your WordPress install and open the folder labelled
                                "wp-content". From there, open the folder "plugins", and drag or copy the "smartcat_our_team" folder into it.
                                Reload your WordPress Dashboard and you will now see a menu option titled "Team".
                            </p>
                        </div>
                        <div class="sub-section">
                            <h3>Method Two</h3>
                            <p>
                                If you want to install the plugin directly from the WordPress Dashboard, you must leave the downloaded file in its
                                current, format (.zip). From the WordPress Dashboard, select "Plugins" from the sidebar menu. At the top of the 
                                next page, click a button labelled "Add New". Click another button, labelled "Upload Plugin", and you 
                                will be directed to a simple page that will let you upload the file. Simply navigate to and choose 
                                the "smartcat_our_team.zip" file, and select "Install Now". Reload your WordPress Dashboard and you will 
                                now see a menu option titled "Team".
                            </p>
                        </div>

                        <h2>Plugin Usage</h2>
                        <div class="sub-section" id="include-sc">
                            <h3>Including the [our-team] Shortcode</h3>
                            <p>
                                To include a showcase display on any page of your site, you simply place the shortcode, "[our-team]" (without quotes) wherever you would like it to appear within the page. 
                            </p>
                            <p>
                                You can also set the group to display as well as full team template as well as the single member template through the shortcode:
                            </p>
                            <p>
                                <strong>[our-team group="Name of Group" template="grid" single_template="panel"]</strong>
                            </p>
                        </div>	
                        <div class="sub-section" id="add-member">
                            <h3>Add a New Member</h3>
                            <p>
                                Selecting "Add New" near the top of the page will open up the Add New Member editor, which will prompt you 
                                for some information. Enter the name of the person you are adding in the first box that asks you to enter a title.
                                The main content box can be used to provide a brief biography of the person, their work history, or the
                                contributions they provide to the team.
                            </p>
                            <p>
                                Additional information, including job title and social media account links may be added using the section below the main content area. A small list of standout skills or proficiencies may also be listed, along with a 1 - 10 rating of that competencty. If you have a Group you would like to add the member to, you may select it from this page.
                            </p>
                            <p>
                                A featured image may be set by clicking the corresponding link. This image will be used as the main portrait photo
                                for the corresponding team member in the different team showcase layouts. For best results, the ideal image size is 300 x 300 pixels.
                            </p>
                            
                            <img style="width: 60%; float: right" src="<?php echo SC_TEAM_URL ?>inc/img/userdetails_demo.jpg"/>
                                             
                            
                            <h3>Member Details</h3>
                            <ul>
                                <li>Name</li>
                                <li>Featured Image</li>
                                <li>Bio</li>
                                <li>Groups</li>
                                <li>Title ( Job Title )</li>
                                <li>Email Address</li>
                                <li>Facebook</li>
                                <li>Twitter</li>
                                <li>LinkedIn</li>
                                <li>Google Plus</li>
                                <li>Personal Quote <em class="pro">Pro Version</em></li>                                
                                <li>Phone Number<em class="pro">Pro Version</em></li>
                            </ul>

                            <img style="width: 60%; float: right" src="<?php echo SC_TEAM_URL ?>inc/img/skills_demo.jpg"/>
                                                          
                            
                            <h3>Attributes / Skills / Ratings</h3>
                            <ul>
                                <li>Skill/Attribute Title<em class="pro">Pro Version</em></li>
                                <li>Skill/Attribute Rating<em class="pro">Pro Version</em></li>
                                
                            </ul>
                            
                            
                        </div>
                        <div class="sub-section" id="manage-members">
                            <h3>Managing Team Members</h3>
                            <p>
                                From the main "Team" page, you will see a list of all currently added team members. Once you have added a member,
                                you may modify their details or change their featured portrait image by clicking on their name, or selecting Edit while
                                hovering over it.
                            </p>
                            <p>
                                From the main "Team" menu, selecting the "Re-Order Members" option will allow you to change the order in which your team members will be displayed. Simply click and drag the member you would like to move, and release the mouse button once that member is in the ideal position.
                                Once the order is set, select Save Order to make your changes final.
                            </p>
                            <p>
                                Should you need to delete a team member from the showcase, you may do so by hovering over the name of the member, then
                                selecting the red "Trash" link. You will not be prompted to confirm your decision, but if you have made a mistake, you may still retrieve the deleted member. After a delete has been performed, there will be a new link titled "Trash (x)", where "x" is the number of pending deleted items. Clicking that link will take you to the Trash, where you may hover over any members and select either "Restore" or "Delete Permanently".
                            </p>
                        </div>
                        <div class="sub-section" id="groups">
                            <h3>Groups</h3>
                            <p>
                                If your company works with a larger team, Our Team Showcase Pro allows you to divide that team into smaller sections based on projects or responsibilities. This is where Groups become helpful, as you can easily display smaller Groups of your team with separate headings.
                            </p>
                            <p>
                                Creating a new Group for your team is easy, and there are two ways you can do it. The first method is to select the "Groups" option from the main "Team" sidebar menu, then enter a name for the Group and click the "Add New Group" button. Alternatively, if you are currently viewing a single team member, the "Groups" box on the right-hand side of the page has an "Add New Group" option. 
                            </p>
                            <p>
                                Using the [our-team] shortcode will default to displaying members regardless of Groups. If you would like to have a showcase that only includes members of a specific group, you must modify the shortcode to include the Group name.
                            </p>
                            <p>
                                Example: [our-team group="name of your group"]
                            </p>
                        </div>
                        <div class="sub-section" id="templates">
                            <h3>Team View Templates</h3>
                            <h4>Grid - Boxes</h4>
                            <p>
                                Displays a grid based layout of team members, with rectangular edges.
                            </p>
                            
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>inc/img/grid_demo.jpg"/>
                            <div class="clear"></div>                                  
                            
                            
                            <h4>Grid - Circles</h4>
                            <p>
                                Displays a grid based layout of team members, with circular edges. Name and Title appear overtop of the circular images.
                            </p>
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>inc/img/circles2_demo.jpg"/>
                            <div class="clear"></div>                              
                            
                            
                            <h4>Grid - Circles Version 2</h4>
                            <p>
                                Displays a grid based layout of team members, with circular edges. Name and Title appear when the mouse hovers over the circular images.
                            </p>
                            
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>inc/img/circles_demo.jpg"/>
                            <div class="clear"></div>                             
                            
                            <h4>List - Stacked <em class="pro">Pro Version</em></h4>
                            <p>
                                Displays a stacked list of each team member, with details such as Name, Title, Description, and Social Media links.
                            </p>
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>screenshot-5.jpg"/>
                            <div class="clear"></div>                             
                            
                            <h4>Honey Comb <em class="pro">Pro Version</em></h4>
                            <p>
                                Displays a honeycomb style layout, consisting of interconnected, hexagonally shaped images that can display information when hovered over.
                            </p>
                            
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>screenshot-4.jpg"/>
                            <div class="clear"></div>                                  
                            
                            <h4>Carousel <em class="pro">Pro Version</em></h4>
                            <p>Displays team members in a horizontally cycling carousel.</p>
                            
                            
                            
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>screenshot-6.jpg"/>
                            <div class="clear"></div>                            
                            
                            <h4>Setting a Template and Using Shortcodes</h4>
                            <p>
                                The default template is "Grid - Boxes", but it can easily be changed to one of several other options. Each one will display your team showcase in a different visual arrangement. To change the default template, select "Settings" under the "Team" menu, and select the desired template from the drop-down list.
                            </p>
                            <p>
                                You can also modify the shortcode with an option to specify a different template than the one selected in the main settings. This is useful if you would like to have multiple different showcase template options on a single page.
                            </p>
                            <p>	
                                Example: [our-team template="grid"]
                            </p>
                            <p>
                                The options that can be placed within the quotes are as follows: grid, grid_circles, hc, stacked, carousel
                            </p>
                            
                            <h3>Single Member View Templates</h3>
                            <h4>Theme Default( Single Post)</h4>
                            <p>This will load the single member page based on your theme's single.php file</p>
                            
                            <h4>Custom Template</h4>
                            <p>This will load the single member page from a custom template file (team_members_template.php)</p>
                            
                            
                            <h4>Card Popup ( vcard ) <em class="pro">Pro Version</em></h4>
                            <p>This will load a lightbox and the member details in a sliding box.</p>
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>inc/img/card_demo.jpg"/>
                            <div class="clear"></div>                            
                            
                            
                            <h4>Side Panel<em class="pro">Pro Version</em></h4>
                            <p>Clicking on a member will slide in a panel that includes all their details in a very appealing design</p>
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>inc/img/panel_demo.jpg"/>
                            <div class="clear"></div>
                        </div>	
                        
                        
                        <div class="sub-section" id="custom_templates">
                            <h3>Custom templates </h3>
                                                        
                            <h4>Overriding the Single Team Member Template</h4>
                            The plugin loads the single team member template based on your theme's single.php file. You can however use the template included in the plugin.
                            Copy /inc/template/single-team_member.php into your theme's root directory.
                            </p>
                            

                            
                            <div>
                                
                                <em class="pro">Pro Version</em> Additionally, you can choose the pre-designed custom template from the settings page
                                
                                <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>inc/img/custom_demo.jpg"/>
                                <div class="clear"></div>                                
                                
                            </div>
                            
                        </div>
                        
                        
                        <div class="sub-section" id="view-settings">
                            <h2>Team View</h2>
                            <h4>Grid Columns:</h4>
                            <p>Specify the number of columns per row of members.</p>

                            <h4>Margin:</h4>
                            <p>Specify the amount of space between each column in a row.</p>
                            <h4>Display Social Icons:</h4>
                            <p>Toggles whether social icons are displayed over the featured images for each member.</p>
                            <h4>Social Icons Links</h4>
                            <p>Specify if you want the social icon links to open in the same page, or in a new page</p>
                            <h4>Social Links Style</h4>
                            <p>Specify if you want to use round colored icons, or flat icons</p>
                            <h4>Display Name:</h4>
                            <p>Toggles whether the member names are displayed over the featured images.</p>
                            <h4>Display Title:</h4>
                            <p>Toggles whether the member's job titles are displayed over the featured images.</p>
                            <h4>Name Font Size: <em class="pro">Pro version</em></h4>
                            <p>Set the font size in pixels. Specify the number value here</p>
                            <h4>Title Font Size: <em class="pro">Pro version</em></h4>
                            <p>Set the font size in pixels. Specify the number value here</p>
                            <h4>Number of members to display:</h4>
                            <p>Specify a limit to the number of displayed members, or -1 to show all members.</p>
                            <h4>Main Color:</h4>
                            <p>Specify the main color, used as the background for member name and job title text.</p>
                            <h4>[ HONEY COMB TEMPLATE ONLY ] Honey Comb Color:<em class="pro">Pro version</em></h4>
                            <p> Specify the color used as the overlay for honey comb shape.</p>
                            <h3>Single Member View</h3>
                            <h4>Template:</h4>
                            <p>
                                This option will set the way that a single member is displayed when selected from the showcase. "Card (pop-up)" displays an index card style display of a single member. A custom template may also be selected from the drop-down menu. The custom template should be a file located in "/inc/template/team_members_template.php".
                            </p>
                            <h4>Display Social Icons:</h4>
                            <p>
                                Toggles whether social icons are displayed when viewing an individual team member.
                            </p>
                            <h4>Display Skills:<em class="pro">Pro version</em></h4>
                            <p>
                                Toggles whether skills are displayed when viewing an individual team member.
                            </p>
                            <h4>Image Style:<em class="pro">Pro version</em></h4>
                            <p>
                                Specifies whether the featured image should have a round or rectangular border, when viewing an individual team member.
                            </p>
                            <h4>Skills Title:<em class="pro">Pro version</em></h4>
                            <p>
                                ALlows you to rename the Skills section to anything you like.
                            </p>
                        </div>
                        
                        <div class="sub-section" id="sidebar-widget">
                            <h3>Sidebar Widget</h3>
                            <p>
                                The plugin comes with an easy to use widget designed for appearing in your site Sidebar.
                                Go to Appearance - Widgets and find the widget titled "Our Team Sidebar Widget".
                                
                            </p>
                            <p>
                                You can drag & drop the widget into any widget placeholder
                            </p>
                            <img style="width: 40%; float: right" src="<?php echo SC_TEAM_URL ?>screenshot-2.jpg"/>
                            <div class="clear"></div>                             
                            
                        </div>

                        <h2 id="faq">Frequently Asked Questions</h2>
                        
                        <div class="sub-section">
                            <h3>What is the recommended Image size for the team members ?</h3>
                            <p>Image size should be 400x400, however you can also use 300x300. Make sure all your images are the same size</p>
                        </div>                        
                        
                        
                        <div class="sub-section">
                            <h3>I can't add a featured image to the team member</h3>
                            <p>The ability to add Featured Images is supported in the plugin, however the way that WordPress Works, the Theme you are using needs to also allow for featured images to be used.

                                Most themes usually allow for featured images, some themes restrict it to single post types, and not custom-post-types.

                                In order to fix this issue, please edit your theme’s functions.php file and add this code to it:</p>
                            <p>
                                <code>function my_custom_theme_setup() {
                                        add_theme_support('post-thumbnails')
                                      }
                                      add_action( 'after_setup_theme', 'my_custom_theme_setup' );
                                </code>
                            </p>
                        </div>

                        <div class="sub-section">
                            <h3>How do I remove the "posted on (date)" from the single member view ?</h3>
                            <p>The date usually comes from your theme's single.php file. If you remove the code snippet from your single.php file,
                            the date will also be removed from your Posts.</p>
                            <p>Alternatively, you can use the Custom Template included in the plugin, which you can select from the plugin settings</p>
                        </div>                        

                        <footer>Copyright © <a href="https://smartcatdesign.net">Smartcat</a></footer>
                    </div>
                    </div>
                    </div>