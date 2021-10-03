<?php
	session_start();

	require_once('lib/redirect.php');
	auth_level(0);

	// Include the database connect file
	require_once('lib/connect.php');
	$mysqli = connectToDatabase();

	// Update the session variables
	$req = 'SELECT bdlc_member, credit FROM users WHERE id = ?';
	if($stmt = $mysqli->prepare($req)) {
		$stmt->bind_param('i', $_SESSION['id']);
		$stmt->execute();

		$stmt->bind_result($bdlc_member, $credit);
		while($stmt->fetch()) {
			$_SESSION['bdlc_member'] = $bdlc_member;
			$_SESSION['credit'] = $credit;
		}
	}
	
	function displayCategory($category) {
		// Get all products
		$mysqli = connectToDatabase();

		$result = $mysqli->query('SELECT * FROM products WHERE category = ' . $category);
		while($row = $result->fetch_assoc()) {
			$actualPrice = ($_SESSION['bdlc_member']) ? $row['bdlc_price'] : $row['price'];
?>
<div class="presentation-card" id="<?php echo htmlspecialchars($row['id']); ?>" onclick="toggleItem(<?php echo htmlspecialchars($category+1); ?>, <?php echo htmlspecialchars($row['id']); ?>)">
  <img class="card-picture" id="card-picture" src="res/images/products/<?php echo htmlspecialchars($row['image']); ?>">
  <div class="content">
      <h4 class="card-name"><?php echo htmlspecialchars($row['name']); ?></h4>
      <h4 class="card-subtitles">Prix unitaire: <?php echo htmlspecialchars($actualPrice); ?> €</h4>
  </div> 
</div>
<?php
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Kfet - Accueil</title>		

		<?php include "templates/head.php"; ?>
		<link rel="stylesheet" type="text/css" href="css/home.css" />
	</head>

	<body>

	<div id="container">
	<?php include "templates/nav.php";?>

	<div class="index-profile clickable" onclick="document.location.href = 'profile.php';">
      <img class="index-profile-picture" src="res/icon.svg">

      <div class="content">
          <h4 class="index-name"><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
          <h4 class="index-money">Solde : <?php echo htmlspecialchars($_SESSION['credit']); ?> €</h4>
      </div>
  </div>

	<div id="home" class="default-linked-section linked-section">
		<!-- Identification de l'étudiant -->
			
    <!-- Evénéments à promouvoir ou des rappels! Exemple : mardi/jeudi viennoiseries, wei, etc -->

		<div class="sub-categories carousel slide " data-ride="carousel" id="event">
			<h4 class="sub-categories-title">Evénements</h4><span class="menuspan" id="span-event"></span>

		  <!-- Indicators -->
		  <ul class="carousel-indicators">
		    <li data-target="#event" data-slide-to="0" class="active"></li>
		    <li data-target="#event" data-slide-to="1"></li>
		    <li data-target="#event" data-slide-to="2"></li>
		  </ul>

		  <!-- The slideshow -->
		  <div class="carousel-inner">
		    <div class="carousel-item active">
		      <img src="res/images/events/bbq.jpg" alt="BBQ">
		    </div>
		    <div class="carousel-item">
		      <img src="https://images.pexels.com/photos/15286/pexels-photo.jpg" alt="Chicago">
		    </div>
		    <div class="carousel-item">
		      <img src="https://images.pexels.com/photos/3408744/pexels-photo-3408744.jpeg" alt="New York">
		    </div>
		  </div>

		  <!-- Left and right controls -->
		  <a class="carousel-control-prev" href="#event" data-slide="prev">
		    <span class="carousel-control-prev-icon"></span>
		  </a>
		  <a class="carousel-control-next" href="#event" data-slide="next">
		    <span class="carousel-control-next-icon"></span>
		  </a>
		</div>

		
<?php
		require_once('lib/favorites.php');
		$favorites = getFavorites($_SESSION['id'], 3);

		if(count($favorites) > 0) {
?>
		<!-- La liste de mes préférés! -->
		<div class="sub-categories">		
			<h4 class="sub-categories-title">J'adore ça, donnez m'en plus!</h4><span class="menuspan" id="span-like"></span>

			<div class="favorites-items">
<?php
						foreach ($favorites as $favorite_id => $quantity) {
							$query = 'SELECT id, image, name, price, bdlc_price, category FROM products WHERE id = ?';
							if($stmt = $mysqli->prepare($query)) {
								$stmt->bind_param('i', $favorite_id);
								$stmt->execute();
								$stmt->bind_result($id, $image, $name, $price, $bdlc_price, $category);

								while ($stmt->fetch()) {
									$actualPrice = ($_SESSION['bdlc_member']) ? $bdlc_price : $price;
									?>

									<div title="<?php echo(htmlspecialchars($name)); ?>" class="sub-presentation-card clickable" id="<?php echo $id; ?>" onclick="addItem(<?php echo $id . ',\'' . $name . '\',\'res/images/products/' . $image . '\',' . $actualPrice; ?>, 1)">
										<img class="card-picture" src="<?php echo 'res/images/products/' . $image ;?>">
										<div class="content">
											<p class="card-name"><?php echo $name;?></p>
											<p class="card-subtitles"><?php echo $actualPrice;?>€</p>
										</div>
									</div>

								<?php
								}
							}
						}
				?>
	
			</div>

		</div>
<?php } ?>
		<!-- La répartition du shop avec mes 4 catégories -->

		<div class="sub-categories" id="categories-items">
			<h4 class="sub-categories-title centered-text underline">Catégories</h4>

			<div class="row">
				<div class="column" style="background-image:url(https://images.pexels.com/photos/585750/pexels-photo-585750.jpeg?auto=compress&cs=tinysrgb&dpr=3&h=750&w=1260);" onclick="window.location = 'index.php#hot-drinks'">
					<p>Boisson<br>Chaude</p>
				</div>
				<div class="column" 
						style="background-image:url(https://images.pexels.com/photos/7235673/pexels-photo-7235673.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940);" onclick="window.location = 'index.php#cold-drinks'">
					<p>Boisson<br>Froide</p>
				</div>
			</div>
			<div class="row">
				<div class="column" style="background-image:url(https://images.pexels.com/photos/4087610/pexels-photo-4087610.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940);" onclick="window.location = 'index.php#snacks'">
					<p>Snacks</p>
				</div>
				<div class="column" style="cursor:not-allowed;background: grey;">
					<p>Formules</p>
				</div>
			</div>		
		</div>
	</div>

	<div id="shop">
		<div id="hot-drinks" class="linked-section">

			<!--Add button to return main menu
			-->
			<div class="return-button clickable" onclick="window.location = 'index.php';">
				<i class="fas fa-undo-alt"></i><p>Retour vers le menu</p>
			</div>

			<div class="header">
	      <h1>Les boissons chaudes</h1>
	    </div>
	   	
	    <?php displayCategory(0); ?>
		</div>
		<div id="cold-drinks" class="linked-section">
			<div class="return-button clickable" onclick="window.location = 'index.php';">
				<i class="fas fa-undo-alt"></i><p>Retour vers le menu</p>
			</div>

			<div class="header">
	      <h1>Les boisons froides</h1>
	    </div>
	  	
	  	<?php displayCategory(1); ?>  
	  </div>
		<div id="snacks" class="linked-section">
			<div class="return-button clickable" onclick="window.location = 'index.php';">
				<i class="fas fa-undo-alt"></i><p>Retour vers le menu</p>
			</div>

			<div class="header">
	      <h1>Les trucs à grignoter</h1>
	    </div>
	   
	   	<?php displayCategory(2); ?>
		</div>
		<div id="formules" class="linked_section" style="display: none;">
			<div class="return-button clickable" onclick="window.location = 'index.php';">
				<i class="fas fa-undo-alt"></i><p>Retour vers le menu</p>
			</div>

			<div class="header">
	      <h1>Formules</h1>
	    </div>
	  
		</div>
  </div>

	</div>
	
	<div id="detailed-item" class="detailed-item" >
		<div class="item-presentation">
			<img class="item-picture" id="item-picture" src="">
			<h2 class="item-name text-center" id="item-name"></h2>
		</div>

		<div class="item-description">
				<h4 class="item-price">Prix unitaire: <span id="item-price"></span>€</h4>
				<h4 class="item-quantity">Quantité: <span id="item-quantity"></span></h4>
				<div class="item-control">
					<div class="item-control-plus" onclick="quantityItem(1)"><i class="fas fa-plus"></i></div>
					<div class="item-control-minus" onclick="quantityItem(-1)"><i class="fas fa-minus"></i></div>
				</div>
				<div class="item-add">
		  		<input type="submit" value="" id="btn-validate-lg">
				</div>
		</div>

  	<div class="close" onclick="toggleItem(0,0)"><i class="fas fa-times"></i></div>
	</div>

	<div class="shoping-cart clickable">
		<div class="icon" onmouseup="toggleShop()">
			<span id="icon" class="fa-layers fa-fw">
		    <i class="fas fa-shopping-cart" id="shopping-cart"></i>
		    <span class="fa-layers-counter" id="number-item"></span>
		  </span>
		</div>

		<div class="icon icon-check" id="check-icon" onmouseup="submitForm()">
			<span id="" class="fa-layers fa-fw">
		    <i class="fas fa-check" id="shopping-cart"></i>
		  </span>
		</div>

		<div id="order-summary">
			<h1 class="text-center">Total panier: <span id="total"></span></h1>

			<form method="post" action="lib/command.php" id="order-form">
    	</form>
    </div>
	</div>	

<script type="text/javascript" src="js/linked_sections.js"></script>
<script type="text/javascript" src="js/shopping.js"></script>
<script src="js/status_message.js"></script>
<script type="text/javascript">
	addStatusMessage('command', {
				'cacolac_1' : 'T\'as vraiment pris un cacolac ??!!!',
				'cacolac_2' : 'Cacolac = meilleure boisson <3',
        'success' : 'Votre commande a été passée avec succès !',
        'database_error_1' : 'Il y a eu un problème avec la base de donnée ... (1)',
        'database_error_2' : 'Il y a eu un problème avec la base de donnée ... (2)',
        'database_error_3' : 'Il y a eu un problème avec la base de donnée ... (3)',
        'database_error_4' : 'Il y a eu un problème avec la base de donnée ... (4)',
        'database_error_5' : 'Il y a eu un problème avec la base de donnée ... (5)',
        'database_error_6' : 'Il y a eu un problème avec la base de donnée ... (6)',
        'not_enough_money' : 'Il semble que vous soyez trop pauvre ! Demandez à un.e barista de vous rajouter de l\'argent.',
        'empty_order' : 'Vous venez vraiment de passer une commande avec rien ?!! --\''
    })

</script>
</body>
</html>