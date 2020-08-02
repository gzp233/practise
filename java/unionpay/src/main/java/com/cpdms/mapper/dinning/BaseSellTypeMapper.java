package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.BaseSellType;
import com.cpdms.model.dinning.BaseSellTypeExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 菜品供应方式 BaseSellTypeMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:49:53
 */
public interface BaseSellTypeMapper {

    @DataFilter(user = false, subDept = true, deptId="bst_dept_id")
    long countByExample(BaseSellTypeExample example);

    int deleteByExample(BaseSellTypeExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseSellType record);

    int insertSelective(BaseSellType record);

    @DataFilter(user = false, subDept = true, deptId="bst_dept_id")
    List<BaseSellType> selectByExample(BaseSellTypeExample example);

    BaseSellType selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BaseSellType record, @Param("example") BaseSellTypeExample example);

    int updateByExample(@Param("record") BaseSellType record, @Param("example") BaseSellTypeExample example);

    int updateByPrimaryKeySelective(BaseSellType record);

    int updateByPrimaryKey(BaseSellType record);

    @DataFilter(user = false, subDept = true, deptId="bst_dept_id")
    List<BaseSellType> selectByFoodId(@Param("foodId") String foodId);

}
