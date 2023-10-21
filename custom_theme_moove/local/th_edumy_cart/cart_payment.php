<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->libdir . '/csvlib.class.php';
require_once $CFG->dirroot . '/local/th_edumy_cart/lib.php';
use core_course\external\course_summary_exporter;

global $DB, $CFG, $COURSE, $USER;

$id = $USER->id;
$id_course = optional_param('id', 0, PARAM_INT);
$param = optional_param('action', '', PARAM_TEXT);


if($param){
    $course_add = $DB->get_record_sql("SELECT *  FROM {course}  WHERE id = $id_course");
    $course_11 = $DB->get_record('course', array('id' => $course_add->id));
    $course_image1 = course_summary_exporter::get_course_image($course_11);
    $course_name1 = html_writer::link(new moodle_url('/course/view.php', array('id' => $course_add->id)),$course_add->fullname);
    $course_add->img = $course_image1;
    $course_add->course_name = $course_name1;
    // print_object($course_add);
    // exit;
}

if (!$course = $DB->get_record('course', array('id' => $COURSE->id))) {
    print_error('invalidcourse', 'local_th_edumy_cart', $COURSE->id);
}

// require_login($COURSE->id);
// require_capability('local/th_edumy_cart:user_view', context_course::instance($COURSE->id));


$PAGE->set_url(new moodle_url('/local/th_edumy_cart/cart_payment.php'));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Thanh toán');
$PAGE->set_title('Thanh toán');
$editurl = new moodle_url('/local/th_edumy_cart/cart_payment.php');
$PAGE->settingsnav->add('Thanh toán', $editurl);


echo $OUTPUT->header();
$style = file_get_contents('style.css');
echo "<style>$style</style>";
echo "<div class = 'course_cart'><h2>Thanh toán</h2></div>";
echo "<div class='content_payment'>";
if(isloggedin() && !isguestuser()){
}else {
    echo "<div class = 'user_info'><h2>Thông tin thanh toán</h2>
    <div >
        <label for='fname'>Họ và tên *:</label>
        <input type='text' id='fname' name='fname' placeholder='Nhập họ tên'><br><br>
    </div>
    <div>
        <label for='lname'>Số điện thoại *:</label>
        <input type='text' id='lname' name='lname' placeholder='Nhập điện thoại'><br><br>
    </div>
    <div>
        <label for='lname'>Email:</label>
        <input type='text' id='lname' name='lname' placeholder='Nhập email'><br><br>
    </div>
    
    </div>";
}

echo '<table class="table payment_methods">
        <thead>
        <tr>
            <th scope="col">Chọn phương thức thanh toán</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <input type="radio" name="option" id="option1" value="Option 1">
                    <label for="option1">Thanh toán bằng chuyển khoản ngân hàng</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="radio" name="option" id="option2" value="Option 2">
                    <label for="option2">Thanh toán bằng ONEPAY</label>
                </td>
            </tr>
            
        </tbody>
     </table>';

echo "</div>";
$list_course = $DB->get_records_sql("SELECT cart.*, c.fullname FROM {local_th_edumy_cart} as cart , {course} as c WHERE user_id = $id AND cart.course_id = c.id");
$list_course1 = [];
foreach($list_course as $course) {
    $course1 = $DB->get_record('course', array('id' => $course->course_id));
    $courseimage = course_summary_exporter::get_course_image($course1);
    $course_name = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->course_id)), $course->fullname);
    $course->img = $courseimage;
    $course->course_name = $course_name;
    $list_course1[] = $course;
}

echo '<div class = "payment_info">';
echo'<h2>Đơn hàng của bạn</h2>';
echo '
    <table class="table" id="product-table">
    <thead>
    <tr>
        <th scope="col">Khóa Học</th>
        <th scope="col">Giá</th>
    </tr>
    </thead>
    <tbody>';

echo '</tbody>
    </table>';

echo '<div class="course_total_price"></div>';



echo '<button id="getValueButton" class="payment_onepay">Tiến hành thanh toán</button>';

echo '</div>';

echo $OUTPUT->footer();

?>

<script type="text/javascript"> 
    $(document).ready(function() { 
        var course_cart_gues = JSON.parse(localStorage.getItem("course_cart"));
        var course_cart_user = <?php echo json_encode($list_course1); ?>;     
        var course_add = <?php echo json_encode($course_add); ?>; 
        var param = <?php echo json_encode($param); ?>; 
        const getValueButton = document.getElementById('getValueButton');
        const productTable = document.getElementById('product-table');
        const VND = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        });

        getValueButton.addEventListener('click', () => {
            // Lấy danh sách tất cả các radio button cùng tên
            const radioButtons = document.getElementsByName('option');

            // Duyệt qua danh sách radio button để tìm radio button được chọn
            let selectedValue;
            for (const radioButton of radioButtons) {
                if (radioButton.checked) {
                selectedValue = radioButton.value;
                break;
                }
            }

            // Kiểm tra xem có radio button nào được chọn hay không
            if (selectedValue) {
                if(param) {
                    var url = <?php echo json_encode($CFG->wwwroot."/local/th_edumy_cart/transfer_information.php?id=$id_course&action=add"); ?>; 
                }else {
                    var url = <?php echo json_encode($CFG->wwwroot."/local/th_edumy_cart/transfer_information.php"); ?>;
                } 
                window.location.href = url;
                
                //console.log('Selected value:', selectedValue);
            } else {
                // console.log('Vui lòng chọn một tùy chọn.');
                confirm("Vui lòng chọn phương thức thanh toán");
            }
        });

        if(param){
             
            const tableRow = document.createElement('tr');

                // Tạo các phần tử <td> chứa thông tin sản phẩm
                const fullNameCell = document.createElement('td');
                fullNameCell.innerHTML = `<div class='course_info'>
                <img src='${course_add.img}'><p> ${course_add.course_name}</p>
                </div>`;

                const priceCell = document.createElement('td');
                priceCell.textContent = `499.000₫`;

                // const descriptionCell = document.createElement('td');
                // descriptionCell.textContent = product.description;

                // const createdAtCell = document.createElement('td');
                // createdAtCell.innerHTML = '';

                tableRow.appendChild(fullNameCell);
                tableRow.appendChild(priceCell);
                //tableRow.appendChild(createdAtCell);

                productTable.appendChild(tableRow);

                var course_actions = document.querySelector('.course_total_price');
            
                var full_price =  VND.format(499000);
                course_actions.innerHTML = `
                        <div class="coupon">
                            <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Mã ưu đãi"> 
                            <button type="submit" class="button" name="apply_coupon" value="Áp dụng">Áp dụng</button>
                        </div>
                        <p class="total_price">Tổng tiền: ${full_price}</p>`;
        }else {
            if(course_cart_user.length  === 0 || course_cart_user == null) {
            course_cart_gues.forEach(product => {
                // Tạo phần tử <tr> mới
                const tableRow = document.createElement('tr');

                // Tạo các phần tử <td> chứa thông tin sản phẩm
                const fullNameCell = document.createElement('td');
                fullNameCell.innerHTML = `<div class='course_info'>
                <img src='${product.img}'><p> ${product.course_name}</p>
                </div>`;

                const priceCell = document.createElement('td');
                priceCell.textContent = `499.000₫`;

                // const descriptionCell = document.createElement('td');
                // descriptionCell.textContent = product.description;

                // const createdAtCell = document.createElement('td');
                // createdAtCell.innerHTML = '';

                tableRow.appendChild(fullNameCell);
                tableRow.appendChild(priceCell);
                //tableRow.appendChild(createdAtCell);

                productTable.appendChild(tableRow);
            });

            var course_actions = document.querySelector('.course_total_price');
            
            var full_price =  VND.format(499000 * course_cart_gues.length);
            console.log(full_price);
            course_actions.innerHTML = `
                    <div class="coupon">
                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Mã ưu đãi"> 
                        <button type="submit" class="button" name="apply_coupon" value="Áp dụng">Áp dụng</button>
                    </div>
                    <p class="total_price">Tổng tiền: ${full_price}</p>`;
        }else {
            course_cart_user.forEach(product => {
                // Tạo phần tử <tr> mới
                const tableRow = document.createElement('tr');

                // Tạo các phần tử <td> chứa thông tin sản phẩm
                const fullNameCell = document.createElement('td');
                fullNameCell.innerHTML = `<div class='course_info'><img src='${product.img}'><p> ${product.course_name}</p></div>`;

                const priceCell = document.createElement('td');
                priceCell.innerHTML = `499.000₫`;

                // const descriptionCell = document.createElement('td');
                // descriptionCell.textContent = product.description;

                // const createdAtCell = document.createElement('td');
                // createdAtCell.innerHTML = '';

                tableRow.appendChild(fullNameCell);
                tableRow.appendChild(priceCell);
                //tableRow.appendChild(createdAtCell);

                productTable.appendChild(tableRow);
            });

            var course_actions = document.querySelector('.course_total_price');
            
            var full_price =  VND.format(499000 * course_cart_user.length);
            console.log(full_price);
            course_actions.innerHTML = `
                    <div class="coupon">
                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Mã ưu đãi"> 
                        <button type="submit" class="button" name="apply_coupon" value="Áp dụng">Áp dụng</button>
                    </div>
                    <p class="total_price">Tổng tiền: ${full_price}</p>`;
            }
        }

        
    });
</script>
