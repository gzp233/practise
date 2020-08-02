package com.cpdms.mapper.examination;

import java.util.List;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.examination.BaseExamination;
import com.cpdms.model.examination.BaseExaminationExample;
import org.apache.ibatis.annotations.Param;

/**
 * 基础体检项目表 BaseExaminationMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:52:40
 */
public interface BaseExaminationMapper {

    @DataFilter(user = false, subDept = true, deptId="be_dept_id")
    long countByExample(BaseExaminationExample example);

    int deleteByExample(BaseExaminationExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseExamination record);

    int insertSelective(BaseExamination record);

    @DataFilter(user = false, subDept = true, deptId="be_dept_id")
    List<BaseExamination> selectByExample(BaseExaminationExample example);

    @DataFilter(user = false, subDept = true, deptId="be_dept_id")
    List<BaseExamination> getList(BaseExamination baseExamination);

    BaseExamination selectByPrimaryKey(String id);

    BaseExamination getOne(String id);

    int updateByExampleSelective(@Param("record") BaseExamination record, @Param("example") BaseExaminationExample example);

    int updateByExample(@Param("record") BaseExamination record, @Param("example") BaseExaminationExample example);

    int updateByPrimaryKeySelective(BaseExamination record);

    int updateByPrimaryKey(BaseExamination record);

}
