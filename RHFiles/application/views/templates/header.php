<html>
	<head>
    <title>RHFiles | <?php echo $page; ?></title>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>/images/icons/site-icon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=ZCOOL+KuaiLe">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/css/reset.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>css/normalize.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/css/skeleton.css">
	  <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
  </head>
  
<body class="header-gap">

    <!-- header -->
  <header>

    <!-- Welcome message and user pic -->
    <h1 id="title">
      <?php if(isset($_SESSION['user_id'])):?>
        <span>Hi,&nbsp;&nbsp;<?php echo $_SESSION['username']; ?> !</span>
        <img id="header-pic" src="<?php echo $_SESSION['user_pic']; ?>" >
      <?php endif; ?>
    </h1>
    
    <!--logo -->
    <div id='logo'><img src="<?php echo base_url(); ?>images/identity/logo.png"></div> 

      <!--Navigation bar -->
      <nav id="nav-menu">
        <ul>
          <?php if(isset($_SESSION['user_id'])):?>
          <li class="border-right"><a href="<?php echo site_url(); ?>profiles/view"><img class="icon" src="<?php echo base_url(); ?>images/icons/dashboard-icon.png">Dashboard</a>
              <ul id="marketplace-dd">
                <li class="full-width"><a href="<?php echo site_url(); ?>profiles/view">Profile</a>
                <li class="full-width"><a href="<?php echo site_url(); ?>pages/view/create_skill">Create Skill</a>
                <li class="full-width"><a href="<?php echo site_url(); ?>listings/view_offers">Listings</a>
                <li class="full-width"><a href="<?php echo site_url(); ?>profiles/search">Search For User</a>
                <li class="full-width"><a href="<?php echo site_url(); ?>trades/view_history">History</a>
              </ul>
          </li>
            <li class="border-right"><a href="<?php echo site_url(); ?>skills/index"><img class="icon" src="<?php echo base_url(); ?>images/icons/marketplace-icon.png">Marketplace</a></li>
            <li class="last-nav-a"><a href="<?php echo site_url(); ?>users/logout"><img class="icon" src="<?php echo base_url(); ?>images/icons/padlock-icon.png">Logout</a></li>
          <?php else: ?>
            <li class="border-right"><a href="<?php echo site_url(); ?>users/register"><img class="icon" src="<?php echo base_url(); ?>images/icons/register-icon.png">Register</a></li>
            <li class="border-right"><a href="<?php echo site_url(); ?>skills/index"><img class="icon" src="<?php echo base_url(); ?>images/icons/marketplace-icon.png">Marketplace</a></li>
            <li id="last-nav-a"><a href="<?php echo site_url(); ?>users/login"><img class="icon" src="<?php echo base_url(); ?>images/icons/login-icon.png">Log In</a></li>
          <?php endif; ?>

        </ul>                   
      </nav>
  </header>

  <!--Flash error messages -->
  <div id="flash-data">
    <?php if($this->session->flashdata('succ_msg')): ?>
      <div class="flash-succ" ><?php echo $this->session->flashdata('succ_msg');?></div>
    <?php elseif($this->session->flashdata('fail_msg')): ?>
      <div class="flash-err" ><?php echo $this->session->flashdata('fail_msg');?></div>
    <?php elseif($this->session->flashdata('warn_msg')):?>
      <div class="flash-warn" ><?php echo $this->session->flashdata('warn_msg'); ?></div>
    <?php endif; //replace with switch? ?>
  </div>

  <!--Main stage -->
  <div id="main-stage" style="padding-bottom:25%;">
 