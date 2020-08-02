package com.cpdms.common.quartz.task;

import com.cpdms.mapper.vote.BaseVoteMapper;
import com.cpdms.model.vote.BaseVote;
import com.cpdms.model.vote.BaseVoteExample;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import java.util.Date;

/**
 *投票结束后停止投票
 * @CLASSNAME   FinishVote
 * @Description 定时调度具体工作类
 * @Auther V
 * @DATE 2019/11/27 11:33
 */
@Component("FinishVote")
public class FinishVote {
    @Autowired
    private BaseVoteMapper baseVoteMapper;

    /**
     * 无参的任务
     */
    public void run() {
        BaseVoteExample baseVoteExample = new BaseVoteExample();
        baseVoteExample.createCriteria().andStatusEqualTo(1).andVoteStatusEqualTo("进行中").andEndTimeLessThanOrEqualTo(new Date());
        BaseVote baseVote = new BaseVote();
        baseVote.setUpdateDate(new Date());
        baseVote.setVoteStatus("已结束");

        baseVoteMapper.updateByExampleSelective(baseVote, baseVoteExample);
    }
}
