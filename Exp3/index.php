<?php
$uploadDir = 'uploads/';
$allowedFileTypes = ['text/plain', 'text/csv', 'text/markdown'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$message = '';
$status = '';

// Ensure the uploads directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['textFile'])) {
    $file = $_FILES['textFile'];
    if ($file['size'] > $maxFileSize) {
        $message = "File exceeds the maximum size of 5MB.";
        $status = "error";
    } elseif (!in_array($file['type'], $allowedFileTypes)) {
        $message = "Invalid file type. Please upload a .txt, .csv, or .md file.";
        $status = "error";
    } elseif ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            $message = "File uploaded successfully!";
            $status = "success";
        } else {
            $message = "Failed to upload the file.";
            $status = "error";
        }
    }
}

// Handle file editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editFileName']) && isset($_POST['fileContent'])) {
    $editFileName = basename($_POST['editFileName']);
    $editFilePath = $uploadDir . $editFileName;

    if (file_exists($editFilePath)) {
        if (file_put_contents($editFilePath, $_POST['fileContent']) !== false) {
            $message = "File edited successfully!";
            $status = "success";
        } else {
            $message = "Failed to edit the file.";
            $status = "error";
        }
    } else {
        $message = "File does not exist.";
        $status = "error";
    }
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteFile'])) {
    $deleteFileName = basename($_POST['deleteFile']);
    $deleteFilePath = $uploadDir . $deleteFileName;

    if (file_exists($deleteFilePath)) {
        if (unlink($deleteFilePath)) {
            $message = "File deleted successfully!";
            $status = "success";
        } else {
            $message = "Failed to delete the file.";
            $status = "error";
        }
    } else {
        $message = "File does not exist.";
        $status = "error";
    }
}

// Get the list of uploaded files
$fileList = array_diff(scandir($uploadDir), ['.', '..']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server File Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eef2f7;
            padding: 20px;
        }
        .container {
            max-width: 900px;
        }
        .header {
            margin-bottom: 20px;
            text-align: center;
        }
        .file-list {
            margin-top: 20px;
        }
        .file-list ul {
            list-style-type: none;
            padding-left: 0;
        }
        .file-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .modal-content {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1 class="text-primary">Server File Manager</h1>
        <p class="text-muted">PHP Internship Hazalto Global Exercise No.3</p>
    </div>

    <!-- Messages -->
    <?php if ($message): ?>
        <div id="messageBox" class="alert <?= $status === 'success' ? 'alert-success' : 'alert-danger' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Upload Form -->
    <form id="uploadForm" enctype="multipart/form-data" method="post" class="mb-4">
        <div class="mb-3">
            <label for="textFile" class="form-label">Upload a file (.txt, .csv, .md):</label>
            <input type="file" id="textFile" name="textFile" class="form-control" accept=".txt, .csv, .md" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    <!-- Search Bar -->
    <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search files...">

    <!-- File List -->
    <div class="file-list">
        <ul id="fileList">
            <?php foreach ($fileList as $file): ?>
                <li>
                    <span><?= $file ?></span>
                    <div>
                        <button class="btn btn-info btn-sm previewBtn" data-file="<?= $file ?>">Preview</button>
                        <button class="btn btn-warning btn-sm editBtn" data-file="<?= $file ?>">Edit</button>
                        <button class="btn btn-danger btn-sm deleteBtn" data-file="<?= $file ?>">Delete</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Modal for File Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="previewContent"></pre>
            </div>
        </div>
    </div>
</div>

<!-- Modal for File Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post">
                    <input type="hidden" id="editFileName" name="editFileName">
                    <div class="mb-3">
                        <textarea id="fileContent" name="fileContent" class="form-control" rows="10"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Auto-hide messages after 5 seconds
    setTimeout(function() {
        $('#messageBox').fadeOut('slow');
    }, 5000);

    // Search files
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#fileList li').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Handle file preview
    $(document).on('click', '.previewBtn', function() {
        let fileName = $(this).data('file');
        
        // Load the file content for preview
        $.get('uploads/' + fileName, function(data) {
            $('#previewContent').text(data);
            $('#previewModal').modal('show');
        }).fail(function() {
            alert("Failed to load the file content.");
        });
    });

    // Handle file edit
    $(document).on('click', '.editBtn', function() {
        let fileName = $(this).data('file');
        $('#editFileName').val(fileName);
        
        // Load the file content into the textarea for editing
        $.get('uploads/' + fileName, function(data) {
            $('#fileContent').val(data);
            $('#editModal').modal('show');
        }).fail(function() {
            alert("Failed to load the file content.");
        });
    });

    // Handle file delete
    $(document).on('click', '.deleteBtn', function() {
        if (confirm("Are you sure you want to delete this file?")) {
            let fileName = $(this).data('file');
            $('<form>', {
                method: 'POST',
                action: 'index.php'
            }).append($('<input>', {
                name: 'deleteFile',
                value: fileName,
                type: 'hidden'
            })).appendTo('body').submit();
        }
    });
});
</script>
</body>
</html>