package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.DailyDishes;
import com.cpdms.model.dinning.DailyDishesExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 每日菜品 DailyDishesMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 16:15:11
 */
public interface DailyDishesMapper {

    @DataFilter(user = false, subDept = true, deptId="dd_dept_id")
    long countByExample(DailyDishesExample example);

    int deleteByExample(DailyDishesExample example);

    int deleteByPrimaryKey(String id);

    int insert(DailyDishes record);

    int insertSelective(DailyDishes record);

    @DataFilter(user = false, subDept = true, deptId="dd_dept_id")
    List<DailyDishes> selectByExample(DailyDishesExample example);

    @DataFilter(user = false, subDept = true, deptId="dd_dept_id")
    List<DailyDishes> dailyList(@Param("groupId") String groupId);

    @DataFilter(user = false, subDept = true, deptId="dd_dept_id")
    List<DailyDishes> selectRelative(@Param("foodName") String foodName);

    @DataFilter(user = false, subDept = true, deptId="dd_dept_id")
    List<DailyDishes> getIdsByGroupId(String groupId);

    DailyDishes selectByPrimaryKey(String id);

    DailyDishes dailyGet(String id);

    @DataFilter(user = false, subDept = true, deptId="dd_dept_id")
    DailyDishes getByGroupAndFood(@Param("groupId") String groupId, @Param("foodId") String foodId);

    int updateByExampleSelective(@Param("record") DailyDishes record, @Param("example") DailyDishesExample example);

    int updateByExample(@Param("record") DailyDishes record, @Param("example") DailyDishesExample example);

    int updateByPrimaryKeySelective(DailyDishes record);

    int updateByPrimaryKey(DailyDishes record);

}
