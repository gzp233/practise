package com.cpdms.mapper.personnel;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.personnel.BasePersonnel;
import java.util.List;

import com.cpdms.model.personnel.BasePersonnelExample;
import org.apache.ibatis.annotations.Param;

/**
 * 行员信息 BasePersonnelMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2020-01-11 10:42:23
 */
public interface BasePersonnelMapper {

    @DataFilter(user = false, subDept = true, deptId="bp_dept_id")
    long countByExample(BasePersonnelExample example);

    int deleteByExample(BasePersonnelExample example);
		
    int deleteByPrimaryKey(String id);
		
    int insert(BasePersonnel record);

    int insertSelective(BasePersonnel record);

    @DataFilter(user = false, subDept = true, deptId="bp_dept_id")
    List<BasePersonnel> selectByExample(BasePersonnelExample example);

    @DataFilter(user = false, subDept = true, deptId="bp_dept_id")
    List<BasePersonnel> selectExists(@Param("cardNo") String cardNo, @Param("personnelNo") String personnelNo, @Param("personnelMobile") String personnelMobile);

    BasePersonnel selectByPrimaryKey(String id);
		
    int updateByExampleSelective(@Param("record") BasePersonnel record, @Param("example") BasePersonnelExample example);

    int updateByExample(@Param("record") BasePersonnel record, @Param("example") BasePersonnelExample example); 
		
    int updateByPrimaryKeySelective(BasePersonnel record);

    int updateByPrimaryKey(BasePersonnel record);
  	  	
}