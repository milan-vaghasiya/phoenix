<form>
    <div class="col-md-12">
        <div class="row">

            <div class="col-md-4 form-group">
                <label for="attendance_date">Attendance Date</label>
                <input type="date" name="attendance_date" id="attendance_date" class="form-control" value="<?=getFyDate()?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="project_id">Project Name</label>
                <select name="project_id" id="project_id" class="form-control basic-select2 req">
                    <option value="">Select Project</option>
                    <?php
                        foreach($projectList as $row):
                            echo '<option value="'.$row->id.'">'.$row->project_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="party_id">Agency Name</label>
                <select name="party_id" id="party_id" class="form-control basic-select2 req">
                    <option value="">Select Agency</option>
                </select>
            </div>

        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Labour Category</th>
                                <th>Shift</th>
                                <th>Male</th>
                                <th>Female</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $i=1;
                                foreach($labourCategoryList as $row):
                                    echo '<tr>
                                        <td>'.$i.'</td>
                                        <td>
                                            '.$row->remark.'
                                            <input type="hidden" name="attendanceData['.$i.'][id]" value="">
                                            <input type="hidden" name="attendanceData['.$i.'][labour_category]" value="'.$row->remark.'">
                                        </td>
                                        <td>
                                            <select name="attendanceData['.$i.'][shift_name]">
                                                <option>DAY</option>
                                                <option>NIGHT</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="attendanceData['.$i.'][male]" value="">
                                        </td>
                                        <td>
                                            <input type="text" name="attendanceData['.$i.'][female]" value="">
                                        </td>
                                    </tr>';
                                    $i++;
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>