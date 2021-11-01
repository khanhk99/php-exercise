<?php
include_once './Term.php';
$term_object = new Term();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_add = $term_object->add();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP1</title>
    <link rel="stylesheet" href="assets/css/semantic.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <div class="ui container">
        <h2>Add product</h2>
        <div class="main">
            <div class="success-message"><?php echo isset($property_add['sucessMessage']) ? $property_add['sucessMessage'] : "" ?></div>
            <div class="error-message"><?php echo isset($property_add['errorMessage']) ? $property_add['errorMessage'] : "" ?></div>
            <div class="ui two column stackable grid">
                <div class="five wide column">
                    <div class="field-option">
                        <!-- <div class="add-field">
                            <button class="ui button blue">Add tag</button>
                        </div> -->
                    </div>
                    <div class="submit-button">
                        <button class="ui button red" onclick="window.localStorage.clear();location.reload()">Clear data</button>
                        <button class="ui button green save-field" form="formData" type="submit">Save</button>
                        <a class="ui button" href="./">Back</a>
                    </div>
                </div>
                <div class="eleven wide column">
                    <form id="formData" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="main-data">
                            <!-- data append -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script>
    const defineFields = {
        tagField: {
            buttonName: 'Add tag',
            titleField: 'Tag',
            attribute: {
                type: "text",
            }
        },
        categoryField: {
            buttonName: 'Add category',
            titleField: 'Category',
            attribute: {
                type: "text",
            }
        }
    }

    const renderForm = (type) => {
        var data = defineFields[type];
        var html = `<div class="data-item input-field" type="${type}">
                        <div class="title-field">
                            <h3>${data.titleField}</h3> 
                            <button class="ui button delete-field" type="button" onclick="deleteField(this)">X</button>
                        </div>
                        <div class="attr-value"> 
                            <input type="${data.attribute.type}" name="${type}[]">
                        </div>
                    </div>`;
        $(".main-data").append(html);
    }

    $(document).ready(function() {
        var savedData = JSON.parse(localStorage.getItem('datas'));
        console.log(savedData);

        var fieldOptionElements = "";
        for (let defineField in defineFields) {
            fieldOptionElements += `<div class="add-field">
            <button class="ui button blue" type="${defineField}">${defineFields[defineField].buttonName}</button>
            </div>`;
        }

        $(".field-option").append(fieldOptionElements);

        $(".add-field button").click(function() {
            var type = $(this).attr("type");
            renderForm(type);
        });

        $(".save-field").click(function() {
            var datas = {};
            var elements = $(".data-item");
            // console.log(elements);
            var count = 0;
            for (var element of elements) {
                count++;
                datas[count] = $(element).attr("type");
            }
            localStorage.setItem('datas', JSON.stringify(datas));
        });

        if (savedData) {
            for (let field in savedData) {
                renderForm(savedData[field])
                // console.log(renderForm(savedData[field]));
            }
            var elements = $(".main-data input");

            for (var element of elements) {
                var name = $(element).attr("name");
                <?php
                $countTag = 0;
                $countCategory = 0;
                ?>
                if (name == "tagField[]") {
                    $(element).attr("value", `<?php echo isset($_POST['tagField'][$countTag]) ? $_POST['tagField'][$countTag] : "";
                                                $countTag++; ?>`);
                }
                if (name == "categoryField[]") {
                    $(element).attr("value", `<?php echo isset($_POST['categoryField'][$countCategory]) ? $_POST['categoryField'][$countCategory] : "";
                                                $countCategory++; ?>`);
                }

            }
        }
    });

    function deleteField(element) {
        $(element).parent().parent().remove();
    }
</script>

</html>