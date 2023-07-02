<?php
/*
 * Hiển thị danh sách người dùng
 */
// xử lí title động
$data = [
    'pageTitle' => 'Danh sách người dùng'
];

// Xử lí lọc bởi lọc phải bao gồm cả phân trang
$filter = '';
if (isGet()) {
    $body = getBody();
    // Xử lí lọc status
    if (!empty($body['status'])) {
        $status = $body['status'];
        if ($status == 2) {
            // Trong database status = 0 là chưa kích hoạt
            $statusSql = 0;
        } else {
            $statusSql = $status;
        }
        if (!empty($filter) && strpos($filter, 'WHERE') !== false) {
            $operator = 'AND';
        } else {
            $operator = 'WHERE';
        }
        $filter .= "WHERE status=$statusSql";
    }
    // Xử lí tìm kiếm
    if (!empty($body['keyword'])) {
        $keyword = $body['keyword'];
        // Vì WHERE đã có trên status nên cần xử lí
        if (!empty($filter) && strpos($filter, 'WHERE') !== false) {
            $operator = 'AND';
        } else {
            $operator = 'WHERE';
        }
        $filter .= " $operator fullname LIKE '%$keyword%'";
    }
}




// Xử lí phân trang
// Lấy ra số lượng bản ghi
$allUserNum = getRows("SELECT id FROM users $filter");
// 1. Xác định được số lượng bản ghi trên 1 trang
$perPage = 4;
// 2. Tính số trang 
$maxPage = ceil($allUserNum / $perPage);
// 3. Xử lí số trang dựa vào phương thức GET
if (!empty(getBody()['page'])) {
    $page = getBody()['page'];
    if ($page < 1 || $page > $maxPage) {
        $page = 1;
    }
} else {
    // Mặc định không truyền là 1
    $page = 1;
}

// 4. Tính toán offset trong limit dựa vào biến $page (offset là giá trị tiếp theo dựa vừa page trước đó ví dụ page=1 thì offset bắt đầu từ 0 sau đó page = 2, chúng ta cần lấy 3 giá trị nên đếm 3 bản ghi và giá trị offset tiếp theo của page =2 là 3)
/**
 * page=1 => offset = 0 => (page-1) * perPage => (1-1) * 3 = 0
 * page=2 => offset = 3 => (page-1) * perPage => (2-1) * 3 = 3
 * page=3 => offset = 6 => (page-1) * perPage => (3-1) * 3 = 6
 */

$offset = ($page - 1) * $perPage;
// Truy vấn lấy tất cả bản ghi
$listAllUser = getDraw("SELECT * FROM users $filter ORDER BY createAt DESC LIMIT $offset, $perPage");

// xử lí lọc, tìm kiếm với phân trang vì khi ta click vào phân trang thì url phía trên sẽ thay đổi và trả về tất cả bản ghi
$queryString = null;
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('module=users', '', $queryString); // bỏ &module=users đi
    $queryString = str_replace('&page=' . $page, '', $queryString); // loại bỏ page vì khi phân trang sẽ xuất hiện page=1, page=2
    $queryString = trim($queryString, '&');
    $queryString = '&' . $queryString;
}
$msg = getFlashData('msg');
$msgType = getFlashData('msg_type');

?>

<?php layout('header', $data); ?>
<div class="container">
    <h1 class="fs-3 mt-5 mb-5 text-center fw-bold text-uppercase">
        <i class="fa-sharp fa-solid fa-list-check"></i>
        <?= $data['pageTitle'] ?? '' ?>
    </h1>
    <?= getMessage($msg, $msgType) ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="d-flex align-items-center m-0">
            <a href="?module=users&action=add" class="btn btn-success">
                Thêm người dùng
                <i class="fa-sharp fa-regular fa-user-plus ms-1"></i>
            </a>
        </p>
        <form action="" method="GET" class="d-flex align-items-center gap-4">
            <!-- Lọc người dùng -->
            <select name="status" class="form-select w-50" aria-label="Default select example">
                <option value="0">Chọn trạng thái</option>
                <option <?= !empty($status) && $status == 1 ? 'selected' : false ?> value="1">Kích hoạt</option>
                <option <?= !empty($status) && $status == 2 ? 'selected' : false ?> value="2">Chưa kích hoạt</option>
            </select>
            <!-- Tìm kiếm người dùng -->

            <div class="d-flex align-items-center">
                <input value="<?= $keyword ?? '' ?>" name="keyword" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button type="" class="btn btn-primary flex-shrink-0">
                    Tìm kiếm
                    <i class="fa-light fa-magnifying-glass"></i>
                </button>
            </div>
            <!-- Xử lí khi nhấn tìm kiếm thì vẫn ở trang gốc -->
            <input name="module" value="users" type="hidden">
        </form>
    </div>
    <table class="table table-primary table-striped">
        <thead>
            <tr class="text-center">
                <th width="8%" scope="col">STT</th>
                <th scope="col">Họ tên</th>
                <th scope="col">Email</th>
                <th scope="col">Điện thoại</th>
                <th width="12%" scope="col">Trạng thái</th>
                <th width="10%" scope="col">Sửa</th>
                <th width="10%" scope="col">Xóa</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($listAllUser)) :
                $count = 0;
                foreach ($listAllUser as $user) :
                    $count++;
            ?>
                    <tr class="table-light text-center">
                        <th scope="row"><?= $count ?></th>
                        <td><?= $user['fullname'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= $user['phone'] ?></td>
                        <td class="text-center"><?= $user['status'] == 1 ? '<btn class="btn btn-success btn-sm">Đã kích hoạt</btn>' : '<btn class="btn btn-warning btn-sm">Chưa kích hoạt</btn>' ?></td>
                        <td>
                            <a class="text-primary" href="?module=users&action=edit&id=<?= $user['id'] ?>">
                                <i class="fa-sharp fa-regular fa-user-pen"></i>
                            </a>
                        </td>
                        <td>
                            <a onclick="return confirm('Bạn có muốn xóa?')" class="text-danger" href="?module=users&action=delete&id=<?= $user['id'] ?>">
                                <i class="fa-sharp fa-regular fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach;
            else :  ?>
                <tr>
                    <td colspan="7">
                        <div class="text-center">Không có người dùng</div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Phân trang -->
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php
            // Xử lí trang trước
            if ($page > 1) {
                $prePage = $page - 1;
                echo '<li class="page-item">
                    <a class="page-link" href="?module=users' . $queryString . '&page=' . $prePage . '">
                       Trước
                    </a>
                </li>';
            }
            ?>

            <?php
            // Giới hạn hiển thị phân trang
            $begin = $page - 2;
            $end = $begin + 3;

            if ($begin < 1) {
                $begin = 1;
                $end = min($maxPage, 4); // Chỉ hiển thị tối đa 4 phần tử
            } elseif ($end > $maxPage) {
                $end = $maxPage;
                $begin = max(1, $end - 3); // Chỉ hiển thị tối đa 4 phần tử
            }
            ?>

            <?php for ($i = $begin; $i <= $end; $i++) { ?>
                <li class="page-item <?= $i == $page ? 'active' : false ?>">
                    <a class="page-link" href="?module=users<?= $queryString ?>&page=<?= $i ?>"><?= $i ?></a>
                    </a>
                </li>
            <?php } ?>

            <?php
            // Xử lí trang sau
            if ($page < $maxPage) {
                $nextPage = $page + 1;
                echo '<li class="page-item">
                    <a class="page-link" href="?module=users' . $queryString . '&page=' . $nextPage . '">
                        Sau
                    </a>
                </li>';
            }
            ?>
        </ul>
    </nav>

</div>
<?php layout('footer'); ?>