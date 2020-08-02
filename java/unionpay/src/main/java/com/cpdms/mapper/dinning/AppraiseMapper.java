package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.Appraise;
import com.cpdms.model.dinning.AppraiseExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 评价表 AppraiseMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-30 15:47:57
 */
public interface AppraiseMapper {

    @DataFilter(user = false, subDept = true, deptId="a_dept_id")
    long countByExample(AppraiseExample example);

    int deleteByExample(AppraiseExample example);

    int deleteByPrimaryKey(String id);

    int insert(Appraise record);

    int insertSelective(Appraise record);

    @DataFilter(user = false, subDept = true, deptId="a_dept_id")
    List<Appraise> selectByExample(AppraiseExample example);

    @DataFilter(user = false, subDept = true, deptId="a_dept_id")
    List<Appraise> selectRelative(@Param(value = "foodName") String foodName);

    Appraise selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") Appraise record, @Param("example") AppraiseExample example);

    int updateByExample(@Param("record") Appraise record, @Param("example") AppraiseExample example);

    int updateByPrimaryKeySelective(Appraise record);

    int updateByPrimaryKey(Appraise record);

}
