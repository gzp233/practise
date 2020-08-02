package com.cpdms.mapper.examination;

import java.util.List;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.examination.BaseHospital;
import com.cpdms.model.examination.BaseHospitalExample;
import org.apache.ibatis.annotations.Param;

/**
 * 医院列表 BaseHospitalMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-18 11:52:17
 */
public interface BaseHospitalMapper {

    @DataFilter(user = false, subDept = true, deptId="bh_dept_id")
    long countByExample(BaseHospitalExample example);

    int deleteByExample(BaseHospitalExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseHospital record);

    int insertSelective(BaseHospital record);

    @DataFilter(user = false, subDept = true, deptId="bh_dept_id")
    List<BaseHospital> selectByExample(BaseHospitalExample example);

    BaseHospital selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BaseHospital record, @Param("example") BaseHospitalExample example);

    int updateByExample(@Param("record") BaseHospital record, @Param("example") BaseHospitalExample example);

    int updateByPrimaryKeySelective(BaseHospital record);

    int updateByPrimaryKey(BaseHospital record);

}
