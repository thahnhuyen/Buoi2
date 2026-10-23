<?php
// Khởi tạo các biến lưu trữ giá trị nhập và thông báo lỗi
$fullname = "";
$attendance = "";
$midterm = "";
$final = "";
$error = "";
$result = null;

// Hàm kiểm tra điểm có nằm trong khoảng từ 0 đến 10 hay không
function isValidScore($score) {
    if ($score === "" || !is_numeric($score)) {
        return false;
    }
    $score = floatval($score);
    return $score >= 0 && $score <= 10;
}

// Hàm tính điểm tổng kết theo công thức: 10% chuyên cần, 30% giữa kỳ, 60% cuối kỳ
function finalScore($attendance, $midterm, $final) {
    return ($attendance * 0.1) + ($midterm * 0.3) + ($final * 0.6);
}

// Xử lý khi người dùng bấm nút Gửi (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"] ?? "");
    $attendance = $_POST["attendance"] ?? "";
    $midterm = $_POST["midterm"] ?? "";
    $final = $_POST["final"] ?? "";

    // Kiểm tra dữ liệu đầu vào
    if ($fullname === "") {
        $error = "Vui lòng nhập họ tên đầy đủ.";
    } elseif (!isValidScore($attendance) || !isValidScore($midterm) || !isValidScore($final)) {
        $error = "Điểm các thành phần phải là số và phải nằm trong khoảng từ 0 đến 10.";
    } else {
        // Chuyển kiểu dữ liệu sang số thực để tính toán
        $att = floatval($attendance);
        $mid = floatval($midterm);
        $fin = floatval($final);

        $total = finalScore($att, $mid, $fin);

        // Xếp loại học phần
        if ($total >= 8.5) {
            $classification = "Giỏi";
        } elseif ($total >= 7.0) {
            $classification = "Khá";
        } elseif ($total >= 5.0) {
            $classification = "Trung bình";
        } else {
            $classification = "Chưa đạt";
        }

        $result = [
            "fullname" => $fullname,
            "total" => round($total, 2),
            "classification" => $classification
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Máy tính điểm học phần</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; background-color: #f4f4f9; }
        .container { max-width: 500px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #3498db; color: white; border: none; padding: 10px 15px; width: 100%; border-radius: 4px; font-size: 16px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; text-align: center; }
        .result { margin-top: 20px; padding: 15px; background: #e8f8f5; border: 1px solid #a3e4d7; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Máy tính điểm học phần</h2>

    <!-- Hiển thị lỗi nếu có -->
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Form nhập liệu, giữ lại giá trị cũ bằng cách gán value="..." -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="fullname">Họ và tên:</label>
            <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($fullname) ?>" required>
        </div>

        <div class="form-group">
            <label for="attendance">Điểm chuyên cần (10%):</label>
            <input type="number" step="0.1" id="attendance" name="attendance" value="<?= htmlspecialchars($attendance) ?>" required>
        </div>

        <div class="form-group">
            <label for="midterm">Điểm giữa kỳ (30%):</label>
            <input type="number" step="0.1" id="midterm" name="midterm" value="<?= htmlspecialchars($midterm) ?>" required>
        </div>

        <div class="form-group">
            <label for="final">Điểm cuối kỳ (60%):</label>
            <input type="number" step="0.1" id="final" name="final" value="<?= htmlspecialchars($final) ?>" required>
        </div>

        <button type="submit">Tính điểm tổng kết</button>
    </form>

    <!-- Hiển thị kết quả tính toán -->
    <?php if ($result): ?>
        <div class="result">
            <h3>Kết quả học tập:</h3>
            <p><strong>Họ và tên:</strong> <?= htmlspecialchars($result["fullname"]) ?></p>
            <p><strong>Điểm tổng kết:</strong> <?= $result["total"] ?></p>
            <p><strong>Xếp loại:</strong> <?= $result["classification"] ?></p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>