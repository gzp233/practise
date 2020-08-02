package com.cpdms.service.vote;

import java.util.Date;
import java.util.HashMap;
import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.vote.BaseVoteItemMapper;
import com.cpdms.mapper.vote.BaseVoteMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.vote.BaseVote;
import com.cpdms.model.vote.BaseVoteExample;
import com.cpdms.model.vote.BaseVoteItem;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 *  BaseVoteService
 * @Title: BaseVoteService.java 
 * @Package com.cpdms.vote.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:40:53  
 **/
@Service
public class BaseVoteService implements BaseService<BaseVote, BaseVoteExample> {
	@Autowired
	private BaseVoteMapper baseVoteMapper;
	@Autowired
    private BaseVoteItemMapper baseVoteItemMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseVote> list(Tablepar tablepar, BaseVoteExample testExample){
	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseVote> list= baseVoteMapper.selectByExample(testExample);
	        PageInfo<BaseVote> pageInfo = new PageInfo<BaseVote>(list);
	        return  pageInfo;
	 }

	 public int saveVote(BaseVote baseVote, List<String> voteItems) {
	     baseVote.setId(SnowflakeIdWorker.getUUID());
	     for (String itemName : voteItems) {
             BaseVoteItem baseVoteItem = new BaseVoteItem();
             baseVoteItem.setId(SnowflakeIdWorker.getUUID());
             baseVoteItem.setItemName(itemName);
             baseVoteItem.setVoteId(baseVote.getId());
             baseVoteItemMapper.insertSelective(baseVoteItem);
         }
         baseVote.setCreateDate(new Date());
	     baseVote.setStartTime(new Date());
	     baseVote.setStatus(1);
	     baseVote.setUpdateDate(new Date());
	     baseVote.setVoteStatus("进行中");

	     return baseVoteMapper.insertSelective(baseVote);
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BaseVoteExample example=new BaseVoteExample();
			BaseVote baseVote =new BaseVote();
			baseVote.setStatus(0);
			baseVote.setUpdateDate(new Date());
			example.createCriteria().andIdIn(lista);
			return baseVoteMapper.updateByExampleSelective(baseVote, example);


	}


	@Override
	public BaseVote selectByPrimaryKey(String id) {

			return baseVoteMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseVote record) {
		return baseVoteMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseVote record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseVoteMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseVote record, BaseVoteExample example) {

		return baseVoteMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseVote record, BaseVoteExample example) {

		return baseVoteMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseVote> selectByExample(BaseVoteExample example) {

		return baseVoteMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseVoteExample example) {

		return baseVoteMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseVoteExample example) {

		return baseVoteMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseVote
	 * @return
	 */
	public int checkNameUnique(BaseVote baseVote){
		BaseVoteExample example=new BaseVoteExample();
		example.createCriteria().andVoteNameEqualTo(baseVote.getVoteName()).andStatusEqualTo(1);
		List<BaseVote> list=baseVoteMapper.selectByExample(example);
		return list.size();
	}


}
