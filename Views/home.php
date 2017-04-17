<?php include 'header.php'; ?>

<!-- Container for student content -->
<div class="container-fluid" id="main_content_div">
	<div class="row">
	  <div class="container" id="status_div">
			<div class="col-sm-2"></div>
			<div class=" col-sm-4 form-group">
			<?php
    $role = $_SESSION['role'];
    if ($role == 'student') {
        echo "<label class='' for='class' >Please tell us what class you are working on.</label>";
        echo "<select class='form-control' id='class'>";


        $courses = $_SESSION['courses'];
        foreach ($courses as $course) {
            echo '<option value = "' . $course->getCourseNumber().'" >' . $course->getCourseName() . '</option>';
        }
    }
              ?>

			</select>


			</div>
			<div class=" col-sm-4 form-group">
			<label class="" for="location">Where are you working today?</label>
			<select class="form-control" id="location">
				<option value="1">CIT Lab</option>
				<option value ="2">Elsewhere</option>
			</select>
			</div>
			<div class="col-sm-2"></div>
	  </div>
	</div>
	<div class="row">

	  <div class="col-lg-3 well" id="student_div">
		 <img src="./Styles/smiley.png" align="left" class="smiley">
		<h4 class="yourName"><?php echo $user->getFirstName(); ?></h4>
		<h4><a href="?action=edit">Edit Profile</a></h4>
	  </div>

	  <div class="col-lg-8 well" id="question_div" style="overflow: auto">
		<table class="table table-condensed table-responsive">


        <?php
          $questions = QuestionDB::getOpenQuestions();

          echo '<tr><th>Questions in queue: ' . count($questions) . '</th><th></th><th></th><th></th></tr>';
          echo '<tr>
                  <th>Course</th>
                  <th>Subject</th>
                  <th>Question</th>
                  <th>Time</th>
               </tr>';


         foreach ($questions as $question) {
             $course = CourseDB::RetrieveCourseByNumber($question->getCourseNumber());
              //$user = $_SESSION['user'];
                if ($question->getUserID() == $user->getUserID()) {
                    echo '<tr class="success">';
                } else {
                    echo '<tr>';
                }
             echo '<td>' . $course->getCourseName() . '</td>' .
                     '<td>' . $question->getSubject() . '</td>' .
                     '<td>' . $question->getDescription() . '</td>' .
                     '<td>' . $question->getAskTime() . '</td>' ;
             if ($question->getUserID() == $user->getUserID() && $role == 'student') {
                 echo '<td><form action="?action=cancel_question" method="post">';
                 echo '<input type="hidden" name="id" value="';
                 echo $question->getQuestionID();
                 echo '?>">';
                 echo '<input class="btn btn-danger" type="submit" name="submit" value="Cancel"></form></td></tr>';
             } elseif ($role == 'tutor') {
                 echo '<td><button value="'. $question->getQuestionID() . '"type="button" class="btn btn-info details" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#myModal">Details</button></td></tr>';
             }
         }

      ?>

		</table>
	  </div>

	</div>
</div>

<!-- Container for Tutor List -->
<div class="container-fluid" id="tutor_list_table_div">
	<div class="row">

			<div class="col-sm-12">
				<h1 id="tutor_header">Available Tutors</h1>
				<hr>
				<table class="table table-hover table-striped" id="tutor_list_table">
				  <thead>
					<tr>
					  <th></th>
					  <th>Name</th>
					  <th>Status</th>
					  <th>Expertise</th>
					</tr>
				  </thead>

          <?php
              $tutors = TutorDB::GetOnlineTutors();

              if ($tutors != null) {
                  foreach ($tutors as $tutor) {
                      echo '<tr><td></td>
                        <td>' . $tutor->getFirstName() . ' ' . $tutor->getLastName() . '</td>' .
                       '<td>' . 'Online' . '</td>' .
                       '<td>' . $tutor->getTutorBio() . '</td>' .
                       '</tr>';
                  }
              } else {
                  echo '<tr><td>There are no available tutors at this time.</td></tr>';
              }
          ?>

				</table>
			</div>

	</div>
</div>
<?php include 'footer.php'; ?>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Question Details</h4>
      </div>

      <div class="modal-body row">
        <div id="modal-body">
			<?php
            if (isset($_SESSION['questionDetails'])) {
                $questionDetails = $_SESSION['questionDetails'];
                echo "<p class='col-xs-12'><strong>Course: </strong>" .$questionDetails->getCourseNumber() . "</p>";
                echo "<p class='col-xs-12'><strong>Subject: </strong>" . $questionDetails->getSubject() . "</p>";
                echo "<p class='col-xs-12'><strong>Question: </strong>" . $questionDetails->getDescription() . "</p>";
                echo "<p class='col-xs-12'><strong>Ask Time: </strong>" . $questionDetails->getAskTime() . "</p>";
                /* display the students name */
            } else {
                echo 'Error displaying question.';
            }
            ?>
		</div>

      </div>
      <div class="modal-footer">
        <button id="closeDetails" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script>
$(document).ready(function() {
      $("#class").change( function(){
              $.ajax({
                      url: ".?action=update_task",
                      type: "POST",
                      data: {"courseNumber": $(this).val()},
             });
      });
	  $("#location").change( function(){
              $.ajax({
                      url: ".?action=update_location",
                      type: "POST",
                      data: {"locationID": $(this).val()},
			  });
	  });
	  $(".details").click(function(){
			$.ajax({
					url: ".?action=details",
					type: "POST",
					data: {"viewQuestion": $(this).val()},
			});
	  });
});
</script>