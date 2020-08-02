package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.FoodNumber;
import com.cpdms.model.dinning.FoodNumberExample;

import java.util.Date;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 菜品期数 FoodNumberMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 16:31:30
 */
public interface FoodNumberMapper {

    @DataFilter(user = false, subDept = true, deptId="fn_dept_id")
    long countByExample(FoodNumberExample example);

    int deleteByExample(FoodNumberExample example);

    int deleteByPrimaryKey(String id);

    int insert(FoodNumber record);

    int insertSelective(FoodNumber record);

    @DataFilter(user = false, subDept = true, deptId="fn_dept_id")
    List<FoodNumber> selectByExample(FoodNumberExample example);

    @DataFilter(user = false, subDept = true, deptId="fn_dept_id")
    FoodNumber getByDate(Date date);

    FoodNumber selectByPrimaryKey(String id);

    @DataFilter(user = false, subDept = true, deptId="fn_dept_id")
    FoodNumber selectByNumber(Integer number);

    int updateByExampleSelective(@Param("record") FoodNumber record, @Param("example") FoodNumberExample example);

    int updateByExample(@Param("record") FoodNumber record, @Param("example") FoodNumberExample example);

    int updateByPrimaryKeySelective(FoodNumber record);

    int updateByPrimaryKey(FoodNumber record);

}
