package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.BaseTimeRange;
import com.cpdms.model.dinning.BaseTimeRangeExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 餐段设置 BaseTimeRangeMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:49:18
 */
public interface BaseTimeRangeMapper {

    @DataFilter(user = false, subDept = true, deptId="btr_dept_id")
    List<BaseTimeRange> selectByExample(BaseTimeRangeExample example);

    @DataFilter(user = false, subDept = true, deptId="btr_dept_id")
    long countByExample(BaseTimeRangeExample example);

    int deleteByExample(BaseTimeRangeExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseTimeRange record);

    int insertSelective(BaseTimeRange record);

    BaseTimeRange selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BaseTimeRange record, @Param("example") BaseTimeRangeExample example);

    int updateByExample(@Param("record") BaseTimeRange record, @Param("example") BaseTimeRangeExample example);

    int updateByPrimaryKeySelective(BaseTimeRange record);

    int updateByPrimaryKey(BaseTimeRange record);

    @DataFilter(user = false, subDept = true, deptId="btr_dept_id")
    List<BaseTimeRange> selectByFoodId(@Param("foodId") String foodId);

}
