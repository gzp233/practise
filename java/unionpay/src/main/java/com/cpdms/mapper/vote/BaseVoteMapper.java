package com.cpdms.mapper.vote;

import java.util.List;

import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.vote.BaseVote;
import com.cpdms.model.vote.BaseVoteExample;
import org.apache.ibatis.annotations.Param;

/**
 *  BaseVoteMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:40:53
 */
public interface BaseVoteMapper {

    @DataFilter(user = false, subDept = true, deptId="bv_dept_id")
    long countByExample(BaseVoteExample example);

    int deleteByExample(BaseVoteExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseVote record);

    int insertSelective(BaseVote record);

    @DataFilter(user = false, subDept = true, deptId="bv_dept_id")
    List<BaseVote> selectByExample(BaseVoteExample example);

    BaseVote selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BaseVote record, @Param("example") BaseVoteExample example);

    int updateByExample(@Param("record") BaseVote record, @Param("example") BaseVoteExample example);

    int updateByPrimaryKeySelective(BaseVote record);

    int updateByPrimaryKey(BaseVote record);

    int increaseNum(String id);
    int decreaseNum(String id);

}
