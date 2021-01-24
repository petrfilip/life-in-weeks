<?php
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

?>

<!DOCTYPE html>
<html>
<head>
  <script src="./dropzone/dropzone.min.js"></script>
  <link rel="stylesheet" href="./dropzone/basic.min.css">
  <link rel="stylesheet" href="./dropzone/dropzone.min.css">
  <link rel="stylesheet" href="style.css">

  <style>
      #gallery {
          box-sizing: content-box;
          margin: 1px;
          border: 1px solid rgba(0, 0, 0, 0.3);
          background: white;
          min-height: 850px;
          display: flex;
          flex-flow: wrap;
          align-items: flex-start;
          justify-content: center;

      }

      #gallery img {
          object-fit: contain;
          max-width: 100%;
          max-height: 100%;
          width: auto;
          height: auto;
      }

      #fileUpload {
          box-sizing: content-box;
          margin: 1px;
          border: 1px solid rgba(0, 0, 0, 0.3);
          background: white;
          min-height: 20px;
          display: none;
      }

  </style>
  <title>Life In Weeks</title>
</head>
<body>
<h1>Life In Weeks</h1>


<?php if (!isset($_GET["year"])): ?>
  <form method="get">
    <input name="year" type="number" min="1900" max="2099" step="1" required value="<?= $_GET["year"] ?>">
    <input type="submit">
  </form>

<?php endif; ?>

<div id="layout">
  <div id="eventDetail">
    <h2><span id="event-details-week-id"><?= $_GET["year"] ?>-01</span></h2>
    <div>
      <label for="event-details-most-common">Most common:</label>
      <textarea id="event-details-most-common"></textarea>
    </div>
    <div>
      <label for="event-details-most-important">Most important:</label>
      <textarea id="event-details-most-important"></textarea>
    </div>
    <div>
      <label>Gallery:</label>
      <div id="gallery" data-gallery="[]"></div>
    </div>
    <div>
      <label></label>
      <div id="fileUpload" class="dropzone disabled"></div>
    </div>
    <div>
      <label></label>
      <input type="submit" id="week-submit" disabled>
    </div>
  </div>
  <div id="liw-wrapper">
      <?php

      $eras = [];
      $weeks = [];


      $eras = loadData("./data/liw-era.json", Era::class);
      $weeks = loadData("./data/liw-week.json", Week::class);

      $shiftedEras = [];


      $startYear = $_GET["year"] ? $_GET["year"] : 2000;
      $endYear = $startYear + 90;
      $currentEra = null;


      $currentWeekNumber = date("W");
      $currentYearNumber = date("Y");
      for ($year = $startYear; $year < $endYear; $year++) {
          $weeksInYear = 52;

          echo '<div class="year-wrapper">';
          echo '<div class="year" id="' . $year . '">' . $year . '</div>';

          if ($currentEra != null) {
              echo '<div class="test" title="ERA: ' . $currentEra->description . '" style="background-color: ' . $currentEra->color . '">';

          }

          for ($week = 1; $week <= $weeksInYear; $week++) {
              $containsWeekEvent = false;
              $isNowWeek = "";
              $weekData = "";

              if (!empty($eras) && isCurrentYearWeek($year, $week, $eras[0]->from) && $currentEra == null) {
                  $currentEra = $eras[0];
                  $shiftedEra = array_shift($eras);
                  array_push($shiftedEras, $shiftedEra);
                  $currentEra->color = sprintf("#%06x", rand(0, 16777215));;
                  echo '<div class="test" title="ERA: ' . $shiftedEra->description . '" style="background-color: ' . $currentEra->color . '">';
              }

              if ($currentYearNumber == $year && $currentWeekNumber == $week) {
                  $isNowWeek = "now-week";
              }

              if (!empty($weeks) && isCurrentYearWeek($year, $week, $weeks[0]->yearWeek)) {
                  $containsWeekEvent = "mc";

                  if (isset($weeks[0]->mostCommon)) {

                      $weekData .= ' data-common="' . $weeks[0]->mostCommon . '"';
                  }

                  if (isset($weeks[0]->mostImportant)) {
                      $weekData .= ' data-important="' . $weeks[0]->mostImportant . '"';
                  }

                  if (isset($weeks[0]->gallery)) {
                      $weekData .= ' data-gallery="' . htmlspecialchars(json_encode($weeks[0]->gallery)) . '"';
                  }

                  if (isset($weeks[0]->mostImportant) || isset($weeks[0]->mostCommon)) {
                      $weekData .= ' title="' . $weeks[0]->yearWeek . '&#10;' . $weeks[0]->mostCommon . '&#10;' . $weeks[0]->mostImportant . '"';
                  }
                  array_shift($weeks);

              } else {
                  $weekData .= ' title="' . $year . '-' . $week . '"';
              }

              echo <<<EOD
<div class="week-wrapper">
<div class="week $containsWeekEvent $isNowWeek" $weekData id="$year-$week">
</div>
</div>
EOD;


              if ($currentEra != null) {
                  if (isCurrentYearWeek($year, $week, $currentEra->to)) {
                      $currentEra = null;
                      echo ' </div > ';
                  }
              }

          }
          echo '</div > ';
          if ($currentEra != null) {
              echo '</div > ';
          }

      }

      function isCurrentYearWeek($currentYear, $currentWeek, $yearWeek)
      {
          $currentEraPieces = explode("-", $yearWeek);
          return $currentEraPieces[0] == $currentYear && $currentEraPieces[1] == $currentWeek;
      }

      function loadData($pathToFile, $clazz)
      {
          $string = file_get_contents($pathToFile);
          $json = json_decode($string, true);

          $newArray = [];
          foreach ($json as $jsonItem) {
              $class = (new ReflectionClass($clazz))->newInstance();
              foreach ($jsonItem as $key => $value) $class->{$key} = $value;
              array_push($newArray, $class);
          }
          return $newArray;
      }


      echo "<div>";
      foreach ($shiftedEras as $shiftedEra) {
          $fontColor = getTextColour($shiftedEra->color);
          echo <<<EOD
<div class="legend-era" style="background-color: $shiftedEra->color; color:$fontColor">
$shiftedEra->description
</div>
EOD;
      }
      echo "</div>";

      function getTextColour($hex)
      {
          list($red, $green, $blue) = sscanf($hex, "#%02x%02x%02x");
          $luma = ($red + $green + $blue) / 3;

          if ($luma < 128) {
              $textcolour = "white";
          } else {
              $textcolour = "black";
          }
          return $textcolour;
      }

      ?>

    <script>

      function updateWeek() {

        if (!selected) {
          return;
        }

        const yearWeek = selected;
        const mostCommon = document.getElementById("event-details-most-common").value;
        const mostImportant = document.getElementById("event-details-most-important").value;

        const galleryData = JSON.parse(document.getElementById("gallery").getAttribute('data-gallery'))

        const data = {
          yearWeek: yearWeek,
          mostCommon: mostCommon,
          mostImportant: mostImportant,
          gallery: galleryData
        }

        postData('./api.php', data)
          .then(data => {
            console.log(data); // JSON data parsed by `data.json()` call

                const updatedElement = document.getElementById(document.getElementById("event-details-week-id").innerHTML);
                updatedElement.classList.add("mc")
                updatedElement.dataset.common = mostCommon;
                updatedElement.dataset.important = mostImportant;
                updatedElement.dataset.gallery = JSON.stringify(galleryData);
                renderGallery(document.getElementById("gallery"), galleryData || [])
                Dropzone.forElement('#fileUpload').removeAllFiles(true)
                console.log(JSON.parse(this.responseText))
          })
          .catch((error) => {
            console.error('Error:', error);
          });

      }

      // Example POST method implementation:
      async function  postData(url = '', data = {}) {
        // Default options are marked with *
        const response = await fetch(url, {
          method: 'POST', // *GET, POST, PUT, DELETE, etc.
          mode: 'cors', // no-cors, *cors, same-origin
          cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
          credentials: 'same-origin', // include, *same-origin, omit
          headers: {
            'Content-Type': 'application/json'
            // 'Content-Type': 'application/x-www-form-urlencoded',
          },
          redirect: 'follow', // manual, *follow, error
          referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
          body: JSON.stringify(data) // body data type must match "Content-Type" header
        });
        return response.json(); // parses JSON response into native JavaScript objects
      }

      var selected = null;

      function mouseoverHandler(event) {
        if (selected) {
          return;
        }
        document.getElementById("event-details-week-id").innerHTML = event.target.id;
        document.getElementById("event-details-most-common").value = event.target.dataset.common !== undefined ? event.target.dataset.common : "";
        document.getElementById("event-details-most-important").value = event.target.dataset.important !== undefined ? event.target.dataset.important : "";
        renderGallery(document.getElementById("gallery"), event.target.dataset.gallery && JSON.parse(event.target.dataset.gallery) || [])
      }

      function onClickHandler(event) {

        if (event.target.id === selected) {
          document.getElementById("fileUpload").style.display = "none";
          // document.getElementById("gallery").style.display = "block";

          document.getElementById(event.target.id).classList.remove("selected");
          selected = null;
          document.getElementById("week-submit").disabled = true;
          return;
        } else if (selected) {
          document.getElementById("fileUpload").style.display = "block";
          // document.getElementById("gallery").style.display = "none";

          Dropzone.forElement('#fileUpload').removeAllFiles(true)
          document.getElementById("week-submit").disabled = false;
          document.getElementById(selected).classList.remove("selected");
          selected = event.target.id
          document.getElementById(event.target.id).classList.add("selected");
        } else {
          document.getElementById("fileUpload").style.display = "block";
          // document.getElementById("gallery").style.display = "none";

          Dropzone.forElement('#fileUpload').removeAllFiles(true)
          document.getElementById("week-submit").disabled = false;
          selected = event.target.id
          document.getElementById(event.target.id).classList.add("selected");
        }
        console.log(selected);
        document.getElementById("event-details-week-id").innerHTML = event.target.id;
        document.getElementById("event-details-most-common").value = event.target.dataset.common !== undefined ? event.target.dataset.common : "";
        document.getElementById("event-details-most-important").value = event.target.dataset.important !== undefined ? event.target.dataset.important : "";
        document.getElementById("gallery").setAttribute('data-gallery', event.target.dataset.gallery || "[]")
        renderGallery(document.getElementById("gallery"), event.target.dataset.gallery && JSON.parse(event.target.dataset.gallery) || [])
      }

      function renderGallery(targetElement, galleryItems) {
        targetElement.innerHTML = '';

        galleryItems.map(item => {
          var img = document.createElement('img');
          img.src = './uploads/thumbs/' + item;
          targetElement.appendChild(img);
        })

        if (galleryItems.length === 0) {
          targetElement.innerHTML = '';
        }
      }

      document.getElementById('week-submit').addEventListener('click', updateWeek)

      document.querySelectorAll('.week').forEach(item => {
        item.addEventListener('mouseover', mouseoverHandler)
      })

      document.querySelectorAll('.week').forEach(item => {
        item.addEventListener('click', onClickHandler)
      })

    </script>
    <script>
      var myDropzone = new Dropzone("#fileUpload", { url: "./file-upload.php" });
      myDropzone.on("success", function(file, response) {
        const galleryElement = document.getElementById("gallery");
        const galleryData = JSON.parse(galleryElement.getAttribute('data-gallery'))
        galleryData.push(response.fileName);
        galleryElement.setAttribute('data-gallery', JSON.stringify(galleryData))
      });

    </script>
  </div>
</div>
</body>
</html>
