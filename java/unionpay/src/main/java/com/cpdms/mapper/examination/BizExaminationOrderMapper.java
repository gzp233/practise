package com.cpdms.mapper.examination;

import java.util.List;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.examination.BizExaminationOrder;
import com.cpdms.model.examination.BizExaminationOrderExample;
import org.apache.ibatis.annotations.Param;

/**
 * 体检预约表 BizExaminationOrderMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:53:05
 */
public interface BizExaminationOrderMapper {

    @DataFilter(user = false, subDept = true, deptId="beo_dept_id")
    long countByExample(BizExaminationOrderExample example);

    int deleteByExample(BizExaminationOrderExample example);

    int deleteByPrimaryKey(String id);

    int insert(BizExaminationOrder record);

    int insertSelective(BizExaminationOrder record);

    @DataFilter(user = false, subDept = true, deptId="beo_dept_id")
    List<BizExaminationOrder> selectByExample(BizExaminationOrderExample example);

    BizExaminationOrder selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BizExaminationOrder record, @Param("example") BizExaminationOrderExample example);

    int updateByExample(@Param("record") BizExaminationOrder record, @Param("example") BizExaminationOrderExample example);

    int updateByPrimaryKeySelective(BizExaminationOrder record);

    int updateByPrimaryKey(BizExaminationOrder record);

    int updateDelayOrder(String date);

}
