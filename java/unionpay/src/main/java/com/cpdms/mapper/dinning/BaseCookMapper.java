package com.cpdms.mapper.dinning;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.dinning.BaseCook;
import com.cpdms.model.dinning.BaseCookExample;
import java.util.List;
import org.apache.ibatis.annotations.Param;

/**
 * 厨师表 BaseCookMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-06 14:25:23
 */
public interface BaseCookMapper {

    @DataFilter(user = false, subDept = true, deptId="bc_dept_id")
    long countByExample(BaseCookExample example);

    int deleteByExample(BaseCookExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseCook record);

    int insertSelective(BaseCook record);

    @DataFilter(user = false, subDept = true, deptId="bc_dept_id")
    List<BaseCook> selectByExample(BaseCookExample example);

    @DataFilter(user = false, subDept = true, deptId="bc_dept_id")
    List<BaseCook> selectCookAndImgs(@Param("cookName") String cookName);

    BaseCook selectByPrimaryKey(String id);
    BaseCook selectOne(String id);

    int updateByExampleSelective(@Param("record") BaseCook record, @Param("example") BaseCookExample example);

    int updateByExample(@Param("record") BaseCook record, @Param("example") BaseCookExample example);

    int updateByPrimaryKeySelective(BaseCook record);

    int updateByPrimaryKey(BaseCook record);

}
