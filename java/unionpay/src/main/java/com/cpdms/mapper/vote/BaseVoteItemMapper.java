package com.cpdms.mapper.vote;

import java.util.List;

import com.cpdms.model.vote.BaseVoteItem;
import com.cpdms.model.vote.BaseVoteItemExample;
import org.apache.ibatis.annotations.Param;

/**
 *  BaseVoteItemMapper
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:42:29
 */
public interface BaseVoteItemMapper {

    long countByExample(BaseVoteItemExample example);

    int deleteByExample(BaseVoteItemExample example);

    int deleteByPrimaryKey(String id);

    int insert(BaseVoteItem record);

    int insertSelective(BaseVoteItem record);

    List<BaseVoteItem> selectByExample(BaseVoteItemExample example);

    BaseVoteItem selectByPrimaryKey(String id);

    int increaseNum(@Param("ids")List<String> ids);
    int decreaseNum(@Param("ids")List<String> ids);

    int updateByExampleSelective(@Param("record") BaseVoteItem record, @Param("example") BaseVoteItemExample example);

    int updateByExample(@Param("record") BaseVoteItem record, @Param("example") BaseVoteItemExample example);

    int updateByPrimaryKeySelective(BaseVoteItem record);

    int updateByPrimaryKey(BaseVoteItem record);

}
