<!-- ส่วนของผู้นิเทศ -->

    <div class="main-card card">
        <div class="form-header card-header text-center">
            <i class="fas fa-file-alt"></i> <span class="fw-bold">แบบบันทึกข้อมูลนิเทศ</span>
        </div>

        <div class="card-body">
            <div class="row g-3">
                
                <div class="col-md-6">
                    <label for="supervisor_name_select" class="form-label fw-bold">ชื่อผู้นิเทศ</label>
                    <select id="supervisor_name_select" name="supervisor_name" class="form-select search-field" onchange="fetchPersonnelData()" required>
                        <option value="">-- กรุณาเลือกชื่อผู้นิเทศ --</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="p_id" class="form-label fw-bold">เลขบัตรประจำตัวประชาชน</label>
                    <input type="text" id="p_id" name="supervisor_p_id" class="form-control display-field" placeholder="--" readonly>
                </div>

                <div class="col-md-6">
                    <label for="agency" class="form-label fw-bold">สังกัด</label>
                    <input type="text" id="agency" name="supervisor_agency" class="form-control display-field" placeholder="--" readonly>
                </div>

                <div class="col-md-6">
                    <label for="position" class="form-label fw-bold">ตำแหน่ง</label>
                    <input type="text" id="position" class="form-control display-field" placeholder="--" readonly>
                </div>
            </div>
        </div>

    <script>
    // ... โค้ด JavaScript เดิม (ไม่จำเป็นต้องแก้ไข) ...
    function populateNameDropdown() {
        const selectElement = document.getElementById('supervisor_name_select');
        
        fetch('fetch_supervisor.php?action=get_names')
            .then(response => response.json())
            .then(names => {
                names.forEach(name => {
                    const option = document.createElement('option');
                    option.value = name; 
                    option.textContent = name;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching names:', error));
    }

    function fetchPersonnelData() {
        // ... (โค้ดเดิม)
        const selectedName = document.getElementById('supervisor_name_select').value; 
        const pidField = document.getElementById('p_id');
        const agencyField = document.getElementById('agency'); 
        const positionField = document.getElementById('position');

        // เคลียร์ข้อมูลเก่าในกรอบสีเหลือง
        pidField.value = '';
        agencyField.value = ''; 
        positionField.value = '';

        if (selectedName) {
            fetch(`fetch_supervisor.php?full_name=${encodeURIComponent(selectedName)}`) 
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // เติมข้อมูลลงในกรอบสีเหลือง
                        pidField.value = result.data.p_id;
                        agencyField.value = result.data.OfficeName; 
                        positionField.value = result.data.position;
                    } else {
                        console.error(result.message);
                        alert('ไม่สามารถดึงข้อมูลบุคลากรได้: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อข้อมูล');
                });
        }
    }

    // เรียกใช้ฟังก์ชันเมื่อ DOM โหลดเสร็จ
    document.addEventListener('DOMContentLoaded', populateNameDropdown);
    </script>