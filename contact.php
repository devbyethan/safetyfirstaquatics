<?php
// OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

$yourEmail = "jhefter@safetyfirstaquatics.com "; // the email address you wish to receive these mails through
$yourWebsite = "safetyfirstaquatics.com"; // the name of your website
$thanksPage = ''; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
$maxPoints = 4; // max points a person can hit before it refuses to submit - recommend 4
$requiredFields = "name,email,comments"; // names of the fields you'd like to be required as a minimum, separate each field with a comma


// DO NOT EDIT BELOW HERE
$error_msg = array();
$result = null;

$requiredFields = explode(",", $requiredFields);

function clean($data) {
	$data = trim(stripslashes(strip_tags($data)));
	return $data;
}
function isBot() {
	$bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

	foreach ($bots as $bot)
		if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
			return true;

	if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
		return true;
	
	return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (isBot() !== false)
		$error_msg[] = "No bots please! UA reported as: ".$_SERVER['HTTP_USER_AGENT'];
		
	// lets check a few things - not enough to trigger an error on their own, but worth assigning a spam score.. 
	// score quickly adds up therefore allowing genuine users with 'accidental' score through but cutting out real spam :)
	$points = (int)0;
	
	$badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");

	foreach ($badwords as $word)
		if (
			strpos(strtolower($_POST['comments']), $word) !== false || 
			strpos(strtolower($_POST['name']), $word) !== false
		)
			$points += 2;
	
	if (strpos($_POST['comments'], "http://") !== false || strpos($_POST['comments'], "www.") !== false)
		$points += 2;
	if (isset($_POST['nojs']))
		$points += 1;
	if (preg_match("/(<.*>)/i", $_POST['comments']))
		$points += 2;
	if (strlen($_POST['name']) < 3)
		$points += 1;
	if (strlen($_POST['comments']) < 15 || strlen($_POST['comments']) > 1500)
		$points += 2;
	if (preg_match("/[bcdfghjklmnpqrstvwxyz]{7,}/i", $_POST['comments']))
		$points += 1;
	// end score assignments

	if ( !empty( $requiredFields ) ) {
		foreach($requiredFields as $field) {
			trim($_POST[$field]);
			
			if (!isset($_POST[$field]) || empty($_POST[$field]) && array_pop($error_msg) != "Please fill in all the required fields and submit again.\r\n")
				$error_msg[] = "Please fill in all the required fields and submit again.";
		}
	}

	if (!empty($_POST['name']) && !preg_match("/^[a-zA-Z-'\s]*$/", stripslashes($_POST['name'])))
		$error_msg[] = "The name field must not contain special characters.\r\n";
	if (!empty($_POST['email']) && !preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower($_POST['email'])))
		$error_msg[] = "That is not a valid e-mail address.\r\n";
	
	if ($error_msg == NULL && $points <= $maxPoints) {
		$subject = "Automatic Form Email";
		
		$message = "You received this e-mail message through your website: \n\n";
		foreach ($_POST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $subval) {
					$message .= ucwords($key) . ": " . clean($subval) . "\r\n";
				}
			} else {
				$message .= ucwords($key) . ": " . clean($val) . "\r\n";
			}
		}
		$message .= "\r\n";
		$message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
		$message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
		$message .= 'Points: '.$points;

		if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
			$headers   = "From: $yourEmail\r\n";
		} else {
			$headers   = "From: $yourWebsite <$yourEmail>\r\n";	
		}
		$headers  .= "Reply-To: {$_POST['email']}\r\n";

		if (mail($yourEmail,$subject,$message,$headers)) {
			if (!empty($thanksPage)) {
				header("Location: $thanksPage");
				exit;
			} else {
				$result = 'Your mail was successfully sent.';
				$disable = true;
			}
		} else {
			$error_msg[] = 'Your mail could not be sent this time. ['.$points.']';
		}
	} else {
		if (empty($error_msg))
			$error_msg[] = 'Your mail looks too much like spam, and could not be sent this time. ['.$points.']';
	}
}
function get_data($var) {
	if (isset($_POST[$var]))
		echo htmlspecialchars($_POST[$var]);
}

?>

<html>

<head>
    <title>Safety First Aquatics | West Bend, WI & Mesa, AZ</title>
    <meta name="description" content="Nation's leading aquatic consultants in the area of providing educational sessions, expert witness consulting and aquatic facility consulting. Learn more here!" />
    <meta name="author" content="Ethan Eisenhard" />
    <link rel="canonical" href="http://www.safetyfirstaquatics.com/" />
    <link rel="alternate" href="http://www.safetyfirstaquatics.com/" hreflang="en-us" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <meta itemprop="addressLocality" content="West Bend">
        <meta itemprop="addressRegion" content="Wisconsin">
        <meta itemprop="addressLocality" content="Mesa">
        <meta itemprop="addressRegion" content="Arizona">
        <meta itemprop="addressCountry" content="United States"></span>
    <meta itemprop="url" content="http://www.safetyfirstaquatics.com/">
    <meta itemprop="email" content="jhefter@safetyfirstaquatics.com ">
    <meta property="og:site_name" content="Juliene R. Hefter" />
    <meta prefix="og: http://ogp.me/ns#" property="og:title" content="Nation's leading aquatic consultants in the area of providing educational sessions, expert witness consulting and aquatic facility consulting. Learn more here!" />
    <meta prefix="og: http://ogp.me/ns#" property="og:description" content="Nation's leading aquatic consultants in the area of providing educational sessions, expert witness consulting and aquatic facility consulting. Learn more here!/">
    <meta prefix="
    og: http://ogp.me/ns#" property="og:image" content="Images/safety_first_logo.jpg" style="height: 627px; width: 1200px;" />
    <meta prefix="og: http://ogp.me/ns#" property="og:url" content="http://www.safetyfirstaquatics.com/">
    <meta prefix="og: http://ogp.me/ns#" property="og:type" content="business" />
    <link rel="stylesheet" href="CSS/lp_template.css">
    <link rel="stylesheet" href="CSS/aos.css">
    <script src="JS/jQuery.js"></script>
    <script src="JS/slick.min.js"></script>
<!--     <script src="JS/mediafit.v2.min.js"></script> -->
    <script src="JS/aos.js"></script>
    <script src="JS/main.js"></script>
    <style>
        p.error, p.success {
			font-weight: bold;
			padding: 10px;
			border: 1px solid;
		}
		p.error {
			background: #ffc0c0;
			color: #900;
		}
		p.success {
			background: #b3ff69;
			color: #4fa000;
		}
    </style>
</head>
<body>

    <div id="header" data-aos="fade-zoom-in"
    data-aos-easing="ease-in-back"
    data-aos-delay="100"
    data-aos-offset="0"></div>

    <div data-aos="fade-zoom-in"
    data-aos-easing="ease-in-back"
    data-aos-delay="200"
    data-aos-offset="0" class = "headfunnel">
    <img class = "right_corner" src="Images/Homepage/waves_corner.png">
        <div class = "headfunnel_container">
            <h1>Contact</h1>
        </div>
    <img class = "left_corner"  src="Images/Homepage/waves_corner.png">
    </div>

    <div class = "content"  data-aos="fade-zoom-in"
    data-aos-easing="ease-in-back"
    data-aos-delay="300"
    data-aos-offset="0">
            <div class = "content_container">
                <div class = "content_desc">
                    <h2>Below you’ll find a contact form to contact Safety First Aquatics.</h2>
                    <?php
                        if (!empty($error_msg)) {
                            echo '<p class="error">ERROR: '. implode("<br />", $error_msg) . "</p>";
                        }
                        if ($result != NULL) {
                            echo '<p class="success">'. $result . "</p>";
                        }
                        ?>

                        <form action="<?php echo basename($path,"contact.php"); ?>" method="post">
                            <noscript>
                                    <p><input type="hidden" name="nojs" id="nojs" /></p>
                            </noscript>
                            <p>
                                <label for="name">Name: *</label> 
                                    <input type="text" name="name" id="name" value="<?php get_data("name"); ?>" /><br />
                                
                                <label for="email">E-mail: *</label> 
                                    <input type="text" name="email" id="email" value="<?php get_data("email"); ?>" /><br />
                                
                                
                                <label for="comments">Comments: *</label>
                                    <textarea name="comments" id="comments" rows="5" cols="20"><?php get_data("comments"); ?></textarea><br />
                            </p>
                            <p>
                                <input type="submit" name="submit" id="submit" value="Send" <?php if (isset($disable) && $disable === true) echo ' disabled="disabled"'; ?> />
                            </p>
                        </form>	
                </div>
                <img src = "Images/LP_Images/template.svg">
            </div>
    </div>

    <div class = "funnel" >
        <div class = "funnel_container">
            <p>Ready to take the next step and schedule your consultation at Derosa Aquatic Consulting, just fill out our contact form or give us a ring at (617)-834-5704.</p>
            <a class = "ghost_button_link reverse funnel" href = "">Schedule Consultation<img src = "Images/Homepage/funnelarrow.svg"></a>
        </div>
    </div>

   

    <div id="footer"></div>

</body>
</html>