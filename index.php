<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Skillmatrix</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>

    <style>
        [data-toggle="buttons"]>.btn>input[type="radio"] {
            display: none;
        }

        body {
            background-color: white;
            color: black;
        }

        body.dark-mode .form-control {
            color: #fff;
            background-color: #201e1e;
        }

        /* Dark Mode Stile */
        body.dark-mode {
            background-color: #121212;
            color: white;
        }

        .notification {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        }

        .btn-group,
        .btn-group-vertical {
            position: relative;
            display: -webkit-inline-box;
            display: -ms-inline-flexbox;
            display: inline-flex;
            vertical-align: middle;
            padding-top: 7px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Skillmatrix</h2>
        <form action="submit.php" method="post">
            <div id="skillList">
                <!-- Skill Row Template -->
                <div class="form-row align-items-center mb-3 skillRow" data-skill-index="0">
                    <div class="col">
                        <input type="text" class="form-control skillInput" placeholder="Kompetenz eingeben">
                    </div>
                    <div class="col">
                        <div class="btn-group" data-toggle="buttons">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <label class="btn btn-outline-success">
                                    <input type="radio" name="kompetenz[0]" value="<?php echo $i; ?>"> <?php echo $i; ?>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="addSkillBtn" class="btn btn-info">+</button>
            <button type="button" id="saveBtn" class="btn btn-success">Speichern</button>
            <button type="button" id="createPdfBtn" class="btn btn-danger">PDF erstellen</button>
            <button type="button" id="toggleDarkMode" class="btn btn-dark">Dark/Light</button>
        </form>


    </div>

    <div id="notification" class="notification">
        Kompetenzen erfolgreich gespeichert!
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        $(function() {
            var availableSkills = ["HTML", "CSS", "JavaScript"];

            function initAutocomplete() {
                $(".skillInput").autocomplete({
                    source: availableSkills,
                    select: function(event, ui) {
                        if (!availableSkills.includes(ui.item.value)) {
                            availableSkills.push(ui.item.value);
                        }
                    }
                });
            }

            $("#addSkillBtn").click(function() {
                var newRow = $(".skillRow:first").clone();
                var newIndex = $("#skillList .skillRow").length;
                newRow.attr("data-skill-index", newIndex);
                newRow.find("input[type='radio']").each(function() {
                    $(this).attr("name", "kompetenz[" + newIndex + "]");
                });
                newRow.find("input[type='text']").val("");
                newRow.find(".btn-group label").removeClass("active");
                newRow.find("input[type='radio']").prop("checked", false);
                $("#skillList").append(newRow);
                initAutocomplete();
            });

            $("#saveBtn").click(function() {
                var skillsData = [];
                $("#skillList .skillRow").each(function() {
                    var skillIndex = $(this).data("skill-index");
                    var skillName = $(this).find(".skillInput").val();
                    var skillLevel = $(this).find("input[type='radio'][name='kompetenz[" + skillIndex + "]']:checked").val();
                    if (skillName) {
                        skillsData.push({
                            skill: skillName,
                            level: skillLevel
                        });
                    }
                });
                localStorage.setItem("savedSkills", JSON.stringify(skillsData));

                // Zeige die Benachrichtigung
                $("#notification").fadeIn();
                setTimeout(function() {
                    $("#notification").fadeOut();
                }, 3000); // Die Benachrichtigung verschwindet nach 3 Sekunden
            });

            function loadSavedSkills() {
                var savedSkills = localStorage.getItem("savedSkills");
                if (savedSkills) {
                    savedSkills = JSON.parse(savedSkills);
                    savedSkills.forEach(function(skillData, index) {
                        var row;
                        if (index === 0) {
                            row = $(".skillRow:first");
                        } else {
                            row = $(".skillRow:first").clone();
                            $("#skillList").append(row);
                        }

                        // Setze den Index für die Zeile und aktualisiere die `name`-Attribute der Radio-Buttons
                        row.attr("data-skill-index", index);
                        row.find("input[type='radio']").each(function() {
                            $(this).attr("name", "kompetenz[" + index + "]");
                        });

                        // Setze die gespeicherten Werte
                        row.find(".skillInput").val(skillData.skill);
                        if (skillData.level) {
                            row.find("input[type='radio'][name='kompetenz[" + index + "]'][value='" + skillData.level + "']").prop("checked", true);
                            row.find("label.btn").removeClass("active");
                            row.find("input[type='radio'][name='kompetenz[" + index + "]'][value='" + skillData.level + "']").parent().addClass("active");
                        }
                    });
                    initAutocomplete();
                }
            }

            loadSavedSkills();
            $(document).ready(function() {
                $('#createPdfBtn').click(function() {
                    const {
                        jsPDF
                    } = window.jspdf;

                    // Konfigurationsoptionen für html2canvas
                    const options = {
                        scale: 2, // Skalierungsfaktor für die Auflösung
                        logging: true, // Aktiviere das Protokollieren von Meldungen
                        useCORS: true // Erlaube die Verwendung von CORS für externe Ressourcen
                    };

                    html2canvas(document.body, options).then(canvas => {
                        const imgData = canvas.toDataURL('image/png');
                        const pdf = new jsPDF();
                        const imgWidth = 210; // Breite des PDF-Dokuments (in mm)
                        const imgHeight = (canvas.height * imgWidth) / canvas.width; // Berechne die Höhe entsprechend dem Seitenverhältnis
                        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
                        pdf.text("Skillmatrix 1.0", 10, 290);
                        pdf.setFontSize(12);

                        // Setze die Schriftart auf kursiv (style: 'italic')
                        pdf.setFont('normal', 'italic');
                        pdf.save("skillmatrix.pdf");
                    });
                });
            });


        });

        const toggleDarkModeButton = document.getElementById('toggleDarkMode');

        const prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (prefersDarkMode) {
            document.body.classList.add('dark-mode');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        toggleDarkModeButton.addEventListener('click', toggleDarkMode);
    </script>
</body>

</html>