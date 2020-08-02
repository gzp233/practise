package com.cpdms.mapper.hair;

import java.util.List;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.hair.BaseHairSetting;
import com.cpdms.model.hair.BaseHairSettingExample;
import org.apache.ibatis.annotations.Param;

/**
 *  BaseHairSettingMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-06 13:00:59
 */
public interface BaseHairSettingMapper {

    @DataFilter(user = false, subDept = true, deptId="bhs_dept_id")
    long countByExample(BaseHairSettingExample example);

    int deleteByExample(BaseHairSettingExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseHairSetting record);

    int insertSelective(BaseHairSetting record);

    @DataFilter(user = false, subDept = true, deptId="bhs_dept_id")
    List<BaseHairSetting> selectByExample(BaseHairSettingExample example);

    BaseHairSetting selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BaseHairSetting record, @Param("example") BaseHairSettingExample example);

    int updateByExample(@Param("record") BaseHairSetting record, @Param("example") BaseHairSettingExample example);

    int updateByPrimaryKeySelective(BaseHairSetting record);

    int updateByPrimaryKey(BaseHairSetting record);

    @DataFilter(user = false, subDept = true, deptId="bhs_dept_id")
    BaseHairSetting selectSetting(@Param("settingKey")String settingKey, @Param("startTime")String startTime);

}
