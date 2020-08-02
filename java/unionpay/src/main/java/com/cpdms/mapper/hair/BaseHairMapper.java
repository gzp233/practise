package com.cpdms.mapper.hair;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.hair.BaseHair;
import com.cpdms.model.hair.BaseHairExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 美发项目表 BaseHairMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:57:06
 */
public interface BaseHairMapper {

    @DataFilter(user = false, subDept = true, deptId="bh_dept_id")
    long countByExample(BaseHairExample example);

    int deleteByExample(BaseHairExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseHair record);

    int insertSelective(BaseHair record);

    @DataFilter(user = false, subDept = true, deptId="bh_dept_id")
    List<BaseHair> selectByExample(BaseHairExample example);

    @DataFilter(user = false, subDept = true, deptId="bh_dept_id")
    List<BaseHair> selectHairAndImgs(BaseHair baseHair);

    BaseHair selectByPrimaryKey(String id);
    BaseHair selectOne(String id);

    int updateByExampleSelective(@Param("record") BaseHair record, @Param("example") BaseHairExample example);

    int updateByExample(@Param("record") BaseHair record, @Param("example") BaseHairExample example);

    int updateByPrimaryKeySelective(BaseHair record);

    int updateByPrimaryKey(BaseHair record);

}
