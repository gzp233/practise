package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.BaseFood;
import com.cpdms.model.dinning.BaseFoodExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 菜品设置 BaseFoodMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:50:37
 */
public interface BaseFoodMapper {

    @DataFilter(user = false, subDept = true, deptId="bf_dept_id")
    long countByExample(BaseFoodExample example);

    int deleteByExample(BaseFoodExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseFood record);

    int insertSelective(BaseFood record);

    @DataFilter(user = false, subDept = true, deptId="bf_dept_id")
    List<BaseFood> selectByExample(BaseFoodExample example);

    @DataFilter(user = false, subDept = true, deptId="bf_dept_id")
    List<BaseFood> selectFoodsAndImgs(List<String> array);

    @DataFilter(user = false, subDept = true, deptId="bf_dept_id")
    List<BaseFood> selectRelative(BaseFood record);

    BaseFood selectByPrimaryKey(String id);

    BaseFood selectOne(String id);

    int updateByExampleSelective(@Param("record") BaseFood record, @Param("example") BaseFoodExample example);

    int updateByExample(@Param("record") BaseFood record, @Param("example") BaseFoodExample example);

    int updateByPrimaryKeySelective(BaseFood record);

    int updateByPrimaryKey(BaseFood record);

}
