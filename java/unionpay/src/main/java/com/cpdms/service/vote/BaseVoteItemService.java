package com.cpdms.service.vote;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.vote.BaseVoteItemMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.vote.BaseVoteItem;
import com.cpdms.model.vote.BaseVoteItemExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 *  BaseVoteItemService
 * @Title: BaseVoteItemService.java 
 * @Package com.cpdms.vote.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:42:29  
 **/
@Service
public class BaseVoteItemService implements BaseService<BaseVoteItem, BaseVoteItemExample> {
	@Autowired
	private BaseVoteItemMapper baseVoteItemMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseVoteItem> list(Tablepar tablepar, String name){
	        BaseVoteItemExample testExample=new BaseVoteItemExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andItemNameLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseVoteItem> list= baseVoteItemMapper.selectByExample(testExample);
	        PageInfo<BaseVoteItem> pageInfo = new PageInfo<BaseVoteItem>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BaseVoteItemExample example=new BaseVoteItemExample();
			example.createCriteria().andIdIn(lista);
			return baseVoteItemMapper.deleteByExample(example);


	}


	@Override
	public BaseVoteItem selectByPrimaryKey(String id) {

			return baseVoteItemMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseVoteItem record) {
		return baseVoteItemMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseVoteItem record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseVoteItemMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseVoteItem record, BaseVoteItemExample example) {

		return baseVoteItemMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseVoteItem record, BaseVoteItemExample example) {

		return baseVoteItemMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseVoteItem> selectByExample(BaseVoteItemExample example) {

		return baseVoteItemMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseVoteItemExample example) {

		return baseVoteItemMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseVoteItemExample example) {

		return baseVoteItemMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseVoteItem
	 * @return
	 */
	public int checkNameUnique(BaseVoteItem baseVoteItem){
		BaseVoteItemExample example=new BaseVoteItemExample();
		example.createCriteria().andItemNameEqualTo(baseVoteItem.getItemName());
		List<BaseVoteItem> list=baseVoteItemMapper.selectByExample(example);
		return list.size();
	}


}
