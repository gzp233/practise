package com.cpdms.service.vote;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.vote.BizVoteMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.vote.BizVote;
import com.cpdms.model.vote.BizVoteExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 *  BizVoteService
 * @Title: BizVoteService.java 
 * @Package com.cpdms.vote.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:42:13  
 **/
@Service
public class BizVoteService implements BaseService<BizVote, BizVoteExample> {
	@Autowired
	private BizVoteMapper bizVoteMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BizVote> list(Tablepar tablepar, String name){
	        BizVoteExample testExample=new BizVoteExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andItemIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BizVote> list= bizVoteMapper.selectByExample(testExample);
	        PageInfo<BizVote> pageInfo = new PageInfo<BizVote>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BizVoteExample example=new BizVoteExample();
			example.createCriteria().andIdIn(lista);
			return bizVoteMapper.deleteByExample(example);


	}


	@Override
	public BizVote selectByPrimaryKey(String id) {

			return bizVoteMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BizVote record) {
		return bizVoteMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BizVote record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return bizVoteMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BizVote record, BizVoteExample example) {

		return bizVoteMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BizVote record, BizVoteExample example) {

		return bizVoteMapper.updateByExample(record, example);
	}

	@Override
	public List<BizVote> selectByExample(BizVoteExample example) {

		return bizVoteMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BizVoteExample example) {

		return bizVoteMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BizVoteExample example) {

		return bizVoteMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param bizVote
	 * @return
	 */
	public int checkNameUnique(BizVote bizVote){
		BizVoteExample example=new BizVoteExample();
		example.createCriteria().andItemIdEqualTo(bizVote.getItemId());
		List<BizVote> list=bizVoteMapper.selectByExample(example);
		return list.size();
	}


}
