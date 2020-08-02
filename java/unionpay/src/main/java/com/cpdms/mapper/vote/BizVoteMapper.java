package com.cpdms.mapper.vote;

import java.util.List;
import java.util.Map;

import com.cpdms.model.vote.BizVote;
import com.cpdms.model.vote.BizVoteExample;
import org.apache.ibatis.annotations.Param;

/**
 *  BizVoteMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:42:13
 */
public interface BizVoteMapper {

    long countByExample(BizVoteExample example);

    int deleteByExample(BizVoteExample example);

    int deleteByPrimaryKey(String id);

    int deleteByCardNoAndItemId(Map<String, Object> map);

    int insert(BizVote record);

    int insertSelective(BizVote record);

    List<BizVote> selectByExample(BizVoteExample example);

    BizVote selectByPrimaryKey(String id);

    int updateByExampleSelective(@Param("record") BizVote record, @Param("example") BizVoteExample example);

    int updateByExample(@Param("record") BizVote record, @Param("example") BizVoteExample example);

    int updateByPrimaryKeySelective(BizVote record);

    int updateByPrimaryKey(BizVote record);

}
